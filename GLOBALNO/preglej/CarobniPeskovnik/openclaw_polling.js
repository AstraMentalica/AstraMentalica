spotna// OpenClaw Polling Client — preverja za nove ukaze od DeepSeek/Llama
// Uporaba: include v MOJ_PESKOVNIK/index.html

const OPENCLAW = {
  gateway_url: 'http://localhost:18789',
  channel_id: 'peskovnik_' + Math.random().toString(36).slice(2,9),
  last_check: null,
  polling_interval: 3000, // 3 sekunde
  timer: null,
  queue: [],

  // Začni polling
  start() {
    this.update_status('connected');
    this.timer = setInterval(() => this.check_commands(), this.polling_interval);
    console.log('[OpenClaw] Polling začenjen:', this.gateway_url);
  },

  // Zaustavi polling
  stop() {
    if (this.timer) clearInterval(this.timer);
    this.update_status('disconnected');
  },

  // Preveri za nove ukaze
  async check_commands() {
    try {
      const url = `${this.gateway_url}/api/channels/${this.channel_id}/commands?since=${this.last_check || ''}`;
      const r = await fetch(url, { method: 'GET', headers: { 'Accept': 'application/json' } });
      if (!r.ok) return;
      const data = await r.json();
      if (data.commands && data.commands.length > 0) {
        data.commands.forEach(cmd => this.execute_command(cmd));
        this.last_check = Date.now();
      }
    } catch (e) {
      // Gateway ni dosegljiv — tišina
    }
  },

  // Izvedi ukaz
  execute_command(cmd) {
    console.log('[OpenClaw] Ukaz:', cmd);
    switch (cmd.action) {
      case 'dodaj_widget':
        dodaj_widget_na_desko(cmd.params.tip);
        break;
      case 'zaženi_meditacijo':
        zaženi_meditacijo(cmd.params.min || 3);
        break;
      case 'postavi_profil':
        Object.assign(stanje.profil, cmd.params.profil);
        osvezi_avatar();
        shrani_profil();
        break;
      case 'ai_chat':
        dodaj_chat_sporocilo('ai', cmd.params.odgovor);
        break;
      case 'runa_dneva':
        runa_dneva();
        break;
      case 'tarot_dneva':
        tarot_dneva();
        break;
      case 'verz_dneva':
        verz_dneva();
        break;
      default:
        console.warn('[OpenClaw] Neznan ukaz:', cmd.action);
    }
  },

  // Pošlji ukaz nazaj na OpenClaw (npr. za DeepSeek odgovor)
  async send_response(action, params = {}) {
    try {
      await fetch(`${this.gateway_url}/api/channels/${this.channel_id}/responses`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action, params, timestamp: Date.now() })
      });
    } catch (e) {
      console.error('[OpenClaw] Napaka pri pošiljanju:', e);
    }
  },

  // Posodobi UI status
  update_status(status) {
    const el = document.getElementById('openclaw-status');
    if (!el) return;
    const colors = { connected: 'var(--zelena)', disconnected: 'var(--roza)', error: '#ef4444' };
    const labels = { connected: '● Povezano', disconnected: '● Ni povezave', error: '● Napaka' };
    el.style.color = colors[status] || colors.error;
    el.textContent = labels[status] || labels.error;
  }
};

// Auto-start ko je DOM pripravljen
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => OPENCLAW.start());
} else {
  OPENCLAW.start();
}