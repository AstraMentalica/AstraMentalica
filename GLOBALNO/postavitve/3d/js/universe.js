/**
 * DATOTEKA: universe.js
 * NAMEN:    Three.js 3D vesolje z planeti (moduli)
 * VERZIJA:  1.0
 * DATUM:    2026-01-01
 */

const Universe = (function() {
    'use strict';
    
    // ========== GLOBALS ==========
    let scene, camera, renderer, controls;
    let planets = [];
    let stars = [];
    let raycaster;
    let mouse;
    let selectedPlanet = null;
    let infoPanel;
    
    // ========== INICIALIZACIJA ==========
    function init() {
        // Scene
        scene = new THREE.Scene();
        scene.background = new THREE.Color(0x050510);
        scene.fog = new THREE.FogExp2(0x050510, 0.0005);
        
        // Camera
        camera = new THREE.PerspectiveCamera(45, window.innerWidth / window.innerHeight, 0.1, 1000);
        camera.position.set(15, 10, 20);
        camera.lookAt(0, 0, 0);
        
        // Renderer
        renderer = new THREE.WebGLRenderer({ antialias: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.shadowMap.enabled = true;
        document.getElementById('universe-container').appendChild(renderer.domElement);
        
        // Raycaster za klik
        raycaster = new THREE.Raycaster();
        mouse = new THREE.Vector2();
        
        // UI panel
        createInfoPanel();
        
        // Naloži podatke in ustvari vesolje
        loadUniverseData();
        
        // Dodaj zvezde
        createStars();
        
        // Dodaj centralno svetlobo
        createLighting();
        
        // Animacija
        animate();
        
        // Eventi
        window.addEventListener('resize', onWindowResize);
        window.addEventListener('click', onMouseClick);
        document.addEventListener('keydown', onKeyDown);
    }
    
    // ========== ZVEZDE ==========
    function createStars() {
        const starGeometry = new THREE.BufferGeometry();
        const starCount = 3000;
        const starPositions = new Float32Array(starCount * 3);
        
        for (let i = 0; i < starCount; i++) {
            starPositions[i*3] = (Math.random() - 0.5) * 800;
            starPositions[i*3+1] = (Math.random() - 0.5) * 400;
            starPositions[i*3+2] = (Math.random() - 0.5) * 200 - 50;
        }
        
        starGeometry.setAttribute('position', new THREE.BufferAttribute(starPositions, 3));
        
        const starMaterial = new THREE.PointsMaterial({
            color: 0xffffff,
            size: 0.2,
            transparent: true,
            opacity: 0.8
        });
        
        const starField = new THREE.Points(starGeometry, starMaterial);
        scene.add(starField);
        
        // Dodaj meglice (razpršene zvezde)
        const nebulaGeometry = new THREE.BufferGeometry();
        const nebulaCount = 500;
        const nebulaPositions = new Float32Array(nebulaCount * 3);
        const nebulaColors = new Float32Array(nebulaCount * 3);
        
        for (let i = 0; i < nebulaCount; i++) {
            nebulaPositions[i*3] = (Math.random() - 0.5) * 300;
            nebulaPositions[i*3+1] = (Math.random() - 0.5) * 150;
            nebulaPositions[i*3+2] = (Math.random() - 0.5) * 150 - 30;
            
            nebulaColors[i*3] = Math.random() * 0.5 + 0.3;
            nebulaColors[i*3+1] = Math.random() * 0.3;
            nebulaColors[i*3+2] = Math.random() * 0.8 + 0.2;
        }
        
        nebulaGeometry.setAttribute('position', new THREE.BufferAttribute(nebulaPositions, 3));
        nebulaGeometry.setAttribute('color', new THREE.BufferAttribute(nebulaColors, 3));
        
        const nebulaMaterial = new THREE.PointsMaterial({ size: 0.15, vertexColors: true, transparent: true, opacity: 0.6 });
        const nebulaField = new THREE.Points(nebulaGeometry, nebulaMaterial);
        scene.add(nebulaField);
    }
    
    // ========== SVETLOBA ==========
    function createLighting() {
        // Ambientna svetloba
        const ambientLight = new THREE.AmbientLight(0x222222);
        scene.add(ambientLight);
        
        // Centralna svetloba (sonce)
        const sunLight = new THREE.PointLight(0xffaa66, 1.5, 50);
        sunLight.position.set(0, 0, 0);
        scene.add(sunLight);
        
        // Dodatne luči
        const fillLight = new THREE.DirectionalLight(0x88aaff, 0.5);
        fillLight.position.set(5, 10, 7);
        scene.add(fillLight);
        
        const backLight = new THREE.DirectionalLight(0xff88aa, 0.3);
        backLight.position.set(-3, -2, -5);
        scene.add(backLight);
    }
    
    // ========== PLANETI ==========
    function createPlanet(data) {
        const geometry = new THREE.SphereGeometry(data.size, 64, 64);
        const material = new THREE.MeshStandardMaterial({
            color: data.color,
            emissive: data.emissive ? 0x442200 : 0x000000,
            emissiveIntensity: data.emissive ? 0.3 : 0,
            roughness: 0.5,
            metalness: 0.1
        });
        
        const planet = new THREE.Mesh(geometry, material);
        planet.position.set(data.position.x, data.position.y, data.position.z);
        planet.userData = {
            id: data.id,
            ime: data.ime,
            opis: data.opis,
            kategorija: data.kategorija,
            orbitRadius: data.orbitRadius,
            orbitSpeed: data.orbitSpeed,
            angle: Math.random() * Math.PI * 2
        };
        
        scene.add(planet);
        
        // Dodaj orbito (krog okoli sonca)
        const orbitPoints = [];
        const orbitRadius = data.orbitRadius || Math.sqrt(data.position.x ** 2 + data.position.z ** 2);
        const orbitSegments = 128;
        
        for (let i = 0; i <= orbitSegments; i++) {
            const angle = (i / orbitSegments) * Math.PI * 2;
            const x = orbitRadius * Math.cos(angle);
            const z = orbitRadius * Math.sin(angle);
            orbitPoints.push(new THREE.Vector3(x, 0, z));
        }
        
        const orbitGeometry = new THREE.BufferGeometry().setFromPoints(orbitPoints);
        const orbitMaterial = new THREE.LineBasicMaterial({ color: 0x4488aa, transparent: true, opacity: 0.3 });
        const orbit = new THREE.LineLoop(orbitGeometry, orbitMaterial);
        scene.add(orbit);
        
        // Dodaj ime planeta (sprite)
        createPlanetLabel(planet, data.ime);
        
        planets.push({
            mesh: planet,
            orbit: orbit,
            orbitRadius: orbitRadius,
            orbitSpeed: data.orbitSpeed || 0.1,
            angle: planet.userData.angle,
            label: null
        });
        
        return planet;
    }
    
    function createPlanetLabel(planet, name) {
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        canvas.width = 256;
        canvas.height = 128;
        
        context.fillStyle = 'rgba(0,0,0,0)';
        context.fillRect(0, 0, canvas.width, canvas.height);
        context.font = 'Bold 24px "Palatino", "Georgia", serif';
        context.fillStyle = '#d4c5a9';
        context.textAlign = 'center';
        context.fillText(name, canvas.width/2, 40);
        
        context.font = '16px "Palatino", "Georgia", serif';
        context.fillStyle = '#b8960c';
        context.fillText('🌍', canvas.width/2, 80);
        
        const texture = new THREE.CanvasTexture(canvas);
        const material = new THREE.SpriteMaterial({ map: texture, transparent: true });
        const sprite = new THREE.Sprite(material);
        sprite.scale.set(1.5, 0.75, 1);
        sprite.position.y = planet.geometry.parameters.radius + 0.5;
        
        planet.add(sprite);
    }
    
    // ========== CENTRALNI CODEX ==========
    function createCodex() {
        const geometry = new THREE.SphereGeometry(2.5, 128, 128);
        const material = new THREE.MeshStandardMaterial({
            color: 0xffd700,
            emissive: 0x442200,
            emissiveIntensity: 0.5,
            metalness: 0.8,
            roughness: 0.2
        });
        
        const codex = new THREE.Mesh(geometry, material);
        codex.userData = {
            id: 'codex',
            ime: 'CODEX',
            opis: 'Srce vsega — lastnikova knjiga modrosti',
            isCenter: true
        };
        scene.add(codex);
        
        // Dodaj svetlobni halo
        const haloGeometry = new THREE.SphereGeometry(2.8, 32, 32);
        const haloMaterial = new THREE.MeshBasicMaterial({
            color: 0xffaa44,
            transparent: true,
            opacity: 0.15,
            side: THREE.BackSide
        });
        const halo = new THREE.Mesh(haloGeometry, haloMaterial);
        codex.add(halo);
        
        // Rotirajoči obroči okoli Codexa
        const ringGeometry = new THREE.TorusGeometry(3.2, 0.08, 64, 200);
        const ringMaterial = new THREE.MeshStandardMaterial({ color: 0xffaa66, emissive: 0x442200 });
        const ring = new THREE.Mesh(ringGeometry, ringMaterial);
        ring.rotation.x = Math.PI / 2;
        codex.add(ring);
        
        const ring2Geometry = new THREE.TorusGeometry(3.5, 0.05, 64, 200);
        const ring2 = new THREE.Mesh(ring2Geometry, ringMaterial);
        ring2.rotation.z = Math.PI / 3;
        ring2.rotation.x = Math.PI / 3;
        codex.add(ring2);
        
        return codex;
    }
    
    // ========== NALOŽI PODATKE ==========
    async function loadUniverseData() {
        try {
            const response = await fetch('/SISTEM/api.php?pot=3d/podatki');
            const data = await response.json();
            
            if (data.uspeh) {
                // Ustvari Codex v centru
                createCodex();
                
                // Ustvari planete
                data.moduli.forEach(modul => {
                    if (!modul.isCenter) {
                        createPlanet(modul);
                    }
                });
            }
        } catch (error) {
            console.error('Napaka pri nalaganju vesolja:', error);
        }
    }
    
    // ========== UI PANEL ==========
    function createInfoPanel() {
        infoPanel = document.createElement('div');
        infoPanel.style.position = 'fixed';
        infoPanel.style.bottom = '20px';
        infoPanel.style.left = '20px';
        infoPanel.style.right = '20px';
        infoPanel.style.background = 'rgba(10, 8, 6, 0.9)';
        infoPanel.style.backdropFilter = 'blur(10px)';
        infoPanel.style.border = '1px solid #b8960c';
        infoPanel.style.borderRadius = '8px';
        infoPanel.style.padding = '15px';
        infoPanel.style.color = '#d4c5a9';
        infoPanel.style.fontFamily = "'Palatino', 'Georgia', serif";
        infoPanel.style.display = 'none';
        infoPanel.style.zIndex = '1000';
        infoPanel.style.maxWidth = '400px';
        infoPanel.style.boxShadow = '0 4px 20px rgba(0,0,0,0.5)';
        
        document.body.appendChild(infoPanel);
    }
    
    function showPlanetInfo(planet) {
        const data = planet.userData;
        
        infoPanel.innerHTML = `
            <h3 style="color: #e8c84a; margin: 0 0 10px 0;">🪐 ${data.ime}</h3>
            <p style="margin: 0 0 10px 0;">${data.opis || 'Raziskuj ta svet znanja.'}</p>
            <p style="font-size: 0.8rem; margin: 0 0 10px 0;">${data.kategorija === 'premium' ? '⭐ Premium vsebina' : '📖 Odprto za vse'}</p>
            <button onclick="Universe.enterPlanet('${data.id}')" style="background: #b8960c; border: none; color: #0a0806; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-weight: bold;">
                🚀 Vstopi v ${data.ime}
            </button>
            <button onclick="Universe.closeInfo()" style="background: transparent; border: 1px solid #b8960c; color: #d4c5a9; padding: 8px 16px; border-radius: 4px; cursor: pointer; margin-left: 10px;">
                Zapri
            </button>
        `;
        
        infoPanel.style.display = 'block';
        selectedPlanet = planet;
    }
    
    function closeInfo() {
        infoPanel.style.display = 'none';
        selectedPlanet = null;
    }
    
    function enterPlanet(planetId) {
        window.location.href = `/?modul=${planetId}&verzija=modernus`;
    }
    
    // ========== KONTROLE ==========
    function onMouseClick(event) {
        mouse.x = (event.clientX / renderer.domElement.clientWidth) * 2 - 1;
        mouse.y = -(event.clientY / renderer.domElement.clientHeight) * 2 + 1;
        
        raycaster.setFromCamera(mouse, camera);
        
        const planetMeshes = planets.map(p => p.mesh);
        const intersects = raycaster.intersectObjects(planetMeshes);
        
        if (intersects.length > 0) {
            const clicked = intersects[0].object;
            showPlanetInfo(clicked);
        } else {
            closeInfo();
        }
    }
    
    function onKeyDown(event) {
        const speed = 0.5;
        switch(event.key) {
            case 'ArrowUp':
                camera.position.y += speed;
                break;
            case 'ArrowDown':
                camera.position.y -= speed;
                break;
            case 'ArrowLeft':
                camera.position.x -= speed;
                break;
            case 'ArrowRight':
                camera.position.x += speed;
                break;
            case '+':
            case '=':
                camera.position.z -= speed;
                break;
            case '-':
                camera.position.z += speed;
                break;
            case 'c':
                camera.position.set(15, 10, 20);
                camera.lookAt(0, 0, 0);
                break;
            case 'Escape':
                closeInfo();
                break;
        }
        camera.lookAt(0, 0, 0);
    }
    
    // ========== ANIMACIJA ==========
    function animate() {
        requestAnimationFrame(animate);
        
        // Rotiraj planete okoli sonca
        planets.forEach(planet => {
            planet.angle += 0.005 * planet.orbitSpeed;
            const x = planet.orbitRadius * Math.cos(planet.angle);
            const z = planet.orbitRadius * Math.sin(planet.angle);
            planet.mesh.position.x = x;
            planet.mesh.position.z = z;
        });
        
        // Rotiraj kamero počasi (opcijsko)
        // camera.position.x = 20 * Math.sin(Date.now() * 0.0002);
        // camera.position.z = 20 * Math.cos(Date.now() * 0.0002);
        // camera.lookAt(0, 0, 0);
        
        renderer.render(scene, camera);
    }
    
    function onWindowResize() {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    }
    
    // ========== JAVNI API ==========
    return {
        init: init,
        enterPlanet: enterPlanet,
        closeInfo: closeInfo
    };
})();

// Ob nalaganju
window.addEventListener('DOMContentLoaded', () => Universe.init());