<?php
// ============================================
// GRADNIK: Glasovno urejanje besedila
// ============================================
?>
<script>
(function() {
    let aktivenEditor = null;
    let diktiranje = false;
    let diktacija = null;
    
    // Poišči aktiven urejevalnik
    function najdiEditor() {
        let editor = document.querySelector('textarea:focus, input:focus, [contenteditable="true"]:focus');
        if (!editor) editor = document.querySelector('textarea, input, [contenteditable="true"]');
        return editor;
    }
    
    // Diktiraj v editor
    function zacniDiktirati() {
        let editor = najdiEditor();
        if (!editor) {
            window.glasGovori('Ni odprtega urejevalnika');
            return;
        }
        
        if (!('webkitSpeechRecognition' in window)) {
            window.glasGovori('Glasovno diktiranje ni podprto');
            return;
        }
        
        aktivenEditor = editor;
        diktiranje = true;
        
        diktacija = new webkitSpeechRecognition();
        diktacija.continuous = true;
        diktacija.interimResults = true;
        diktacija.lang = 'sl-SI';
        
        diktacija.onresult = (e) => {
            let koncno = '';
            for (let i = e.resultIndex; i < e.results.length; i++) {
                if (e.results[i].isFinal) koncno += e.results[i][0].transcript + ' ';
            }
            if (koncno && aktivenEditor) {
                let start = aktivenEditor.selectionStart;
                let end = aktivenEditor.selectionEnd;
                let vrednost = aktivenEditor.value;
                aktivenEditor.value = vrednost.substring(0, start) + koncno + vrednost.substring(end);
                aktivenEditor.selectionStart = aktivenEditor.selectionEnd = start + koncno.length;
                aktivenEditor.dispatchEvent(new Event('input'));
            }
        };
        
        diktacija.onend = () => {
            diktiranje = false;
            window.glasGovori('Diktiranje končano');
        };
        
        diktacija.start();
        window.glasGovori('Diktiram. Govori besedilo.');
    }
    
    function nehajDiktirati() {
        if (diktacija) diktacija.stop();
        diktiranje = false;
    }
    
    // Ukazi za urejanje
    window.addEventListener('glas-ukaz', (e) => {
        let u = e.detail.ukaz.toLowerCase();
        
        if (u.includes('diktiraj') || u.includes('začni pisati')) {
            zacniDiktirati();
            return;
        }
        
        if (u.includes('nehaj') || u.includes('ustavi diktiranje')) {
            nehajDiktirati();
            return;
        }
        
        if (u.includes('počisti vse') || u.includes('izbriši vse')) {
            let editor = najdiEditor();
            if (editor) {
                editor.value = '';
                window.glasGovori('Vse izbrisano');
            }
            return;
        }
        
        if (u.includes('izbriši zadnjo') || u.includes('zbriši zadnjo besedo')) {
            let editor = najdiEditor();
            if (editor && editor.value) {
                let besede = editor.value.trim().split(/\s+/);
                besede.pop();
                editor.value = besede.join(' ');
                window.glasGovori('Zadnja beseda izbrisana');
            }
            return;
        }
        
        if (u.includes('nova vrstica') || u.includes('enter')) {
            let editor = najdiEditor();
            if (editor) {
                let start = editor.selectionStart;
                let vrednost = editor.value;
                editor.value = vrednost.substring(0, start) + '\n' + vrednost.substring(start);
                editor.selectionStart = editor.selectionEnd = start + 1;
                window.glasGovori('Nova vrstica');
            }
            return;
        }
        
        if (u.includes('presledek')) {
            let editor = najdiEditor();
            if (editor) {
                let start = editor.selectionStart;
                let vrednost = editor.value;
                editor.value = vrednost.substring(0, start) + ' ' + vrednost.substring(start);
                editor.selectionStart = editor.selectionEnd = start + 1;
            }
            return;
        }
    });
})();
</script>