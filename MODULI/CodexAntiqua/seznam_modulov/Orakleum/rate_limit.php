<?php
class RateLimiter {
    private $limit;
    private $timeframe;
    private $logFile;
    
    public function __construct($limit = 5, $timeframe = 3600, $logFile = 'rate_limit.log') {
        $this->limit = $limit;
        $this->timeframe = $timeframe;
        $this->logFile = LOG_PATH . $logFile;
    }
    
    public function check($identifier) {
        $now = time();
        $attempts = $this->getAttempts($identifier);
        
        // Odstrani stare poskuse
        $recentAttempts = array_filter($attempts, function($time) use ($now) {
            return $time > $now - $this->timeframe;
        });
        
        // Preveri, če je presežena omejitev
        if (count($recentAttempts) >= $this->limit) {
            return false;
        }
        
        // Zabeleži nov poskus
        $recentAttempts[] = $now;
        $this->saveAttempts($identifier, $recentAttempts);
        
        return true;
    }
    
    private function getAttempts($identifier) {
        $data = [];
        if (file_exists($this->logFile)) {
            $data = json_decode(file_get_contents($this->logFile), true) ?: [];
        }
        
        return $data[$identifier] ?? [];
    }
    
    private function saveAttempts($identifier, $attempts) {
        $data = [];
        if (file_exists($this->logFile)) {
            $data = json_decode(file_get_contents($this->logFile), true) ?: [];
        }
        
        $data[$identifier] = $attempts;
        file_put_contents($this->logFile, json_encode($data), LOCK_EX);
    }
}
?>r4