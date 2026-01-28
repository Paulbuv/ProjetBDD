document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');
    
    // Éléments de l'interface
    const colorInput = document.getElementById('color');
    const sizeInput = document.getElementById('size');
    const opacityInput = document.getElementById('opacity');
    const brushBtn = document.getElementById('brushBtn');
    const eraserBtn = document.getElementById('eraserBtn');
    const rectBtn = document.getElementById('rectBtn');
    const undoBtn = document.getElementById('undo');

    let painting = false;
    let currentMode = 'brush'; // modes: brush, eraser, rect
    let startX, startY; // Pour les formes
    let historyStack = []; // Pour l'annulation (Undo)

    // Sauvegarder l'état initial
    saveState();

    function saveState() {
        if (historyStack.length > 20) historyStack.shift(); // Limite la mémoire
        historyStack.push(canvas.toDataURL());
    }

    // Fonction pour obtenir les coordonnées correctes en tenant compte du scaling
    function getCanvasCoordinates(e) {
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        
        const clientX = e.clientX || (e.touches && e.touches[0] ? e.touches[0].clientX : null);
        const clientY = e.clientY || (e.touches && e.touches[0] ? e.touches[0].clientY : null);
        
        if (clientX === null || clientY === null) {
            return { x: 0, y: 0 };
        }
        
        return {
            x: (clientX - rect.left) * scaleX,
            y: (clientY - rect.top) * scaleY
        };
    }

    function startPosition(e) {
        painting = true;
        const coords = getCanvasCoordinates(e);
        startX = coords.x;
        startY = coords.y;
        
        if (currentMode !== 'rect') draw(e);
    }

    function finishedPosition(e) {
        if (!painting) return;
        
        if (currentMode === 'rect') {
            const coords = e.changedTouches 
                ? getCanvasCoordinates({ changedTouches: [e.changedTouches[0]] })
                : getCanvasCoordinates(e);
            drawRectangle(startX, startY, coords.x, coords.y);
        }

        painting = false;
        ctx.beginPath();
        saveState(); // On sauvegarde après chaque action terminée
    }

    function draw(e) {
        if (!painting || currentMode === 'rect') return;

        const coords = getCanvasCoordinates(e);

        ctx.lineWidth = sizeInput.value;
        ctx.lineCap = 'round';
        ctx.globalAlpha = opacityInput.value;

        if (currentMode === 'eraser') {
            ctx.globalCompositeOperation = 'destination-out'; // "Efface" les pixels
        } else {
            ctx.globalCompositeOperation = 'source-over';
            ctx.strokeStyle = colorInput.value;
        }

        ctx.lineTo(coords.x, coords.y);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(coords.x, coords.y);
    }
 
    function drawRectangle(x1, y1, x2, y2) {
        ctx.globalCompositeOperation = 'source-over';
        ctx.globalAlpha = opacityInput.value;
        ctx.strokeStyle = colorInput.value;
        ctx.lineWidth = sizeInput.value;
        ctx.strokeRect(x1, y1, x2 - x1, y2 - y1);
    }
 
    // Changement de mode
    brushBtn.onclick = () => { currentMode = 'brush'; updateUI(brushBtn); };
    eraserBtn.onclick = () => { currentMode = 'eraser'; updateUI(eraserBtn); };
    rectBtn.onclick = () => { currentMode = 'rect'; updateUI(rectBtn); };
 
    function updateUI(activeBtn) {
        [brushBtn, eraserBtn, rectBtn].forEach(btn => {
            btn.classList.replace('primary', 'ghost');
        });
        activeBtn.classList.replace('ghost', 'primary');
    }
 
    // Undo
    undoBtn.onclick = () => {
        if (historyStack.length > 1) {
            historyStack.pop(); // Enlever l'état actuel
            let prevImg = new Image();
            prevImg.src = historyStack[historyStack.length - 1];
            prevImg.onload = () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.globalCompositeOperation = 'source-over';
                ctx.globalAlpha = 1.0;
                ctx.drawImage(prevImg, 0, 0);
            };
        }
    };
 
    // Events
    canvas.addEventListener('mousedown', startPosition);
    canvas.addEventListener('mousemove', draw);
    window.addEventListener('mouseup', finishedPosition);
 
    // Effacer
    document.getElementById('clear').onclick = () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        saveState();
    };
 
    // Save
    document.getElementById('save').onclick = () => {
        const link = document.createElement('a');
        link.download = 'concours-dessin.png';
        link.href = canvas.toDataURL();
        link.click();
    };
});