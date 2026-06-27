<div id="aiChat">
    <div id="chatZgodovina"></div>
    <input type="text" id="chatVnos" placeholder="Vprašaj AI...">
    <button onclick="posljiSporocilo()">Pošlji</button>
</div>
<script>
async function posljiSporocilo() {
    let vnos = document.getElementById('chatVnos').value;
    let odgovor = await fetch('/api/v1/ai/chat', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({sporocilo: vnos})
    }).then(r => r.json());
    // prikaži odgovor
}
</script>