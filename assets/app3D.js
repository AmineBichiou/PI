import './styles/app.css';
import * as THREE from 'three';
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls';
import { GLTFExporter } from 'three/examples/jsm/exporters/GLTFExporter';

const { jsPDF } = window.jspdf;

document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('warehouse-canvas');
    if (!canvas) return;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(45, canvas.clientWidth / canvas.clientHeight, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ canvas, antialias: true });
    renderer.setSize(canvas.clientWidth, canvas.clientHeight);
    renderer.setClearColor(0xffffff);

    const controls = new OrbitControls(camera, renderer.domElement);
    controls.target.set(0, 5, 0);

    // Retrieve data from Twig
    const data = {
        espace: parseFloat('{{ data.espace }}') || 300,
        height: parseFloat('{{ data.height }}') || 10,
        aisles: parseInt('{{ data.aisles }}') || 2,
        storageType: '{{ data.storageType }}' || 'Pallets',
        wallColor: '{{ data.wallColor }}' || '#d3d3d3',
        floorColor: '{{ data.floorColor }}' || '#696969',
    };

    let state = {
        dimensions: {
            length: Math.sqrt(data.espace) * 1.333, // Assume length = 4/3 * width
            width: Math.sqrt(data.espace) / 1.333,
            height: data.height,
        },
        aisles: data.aisles,
        storageType: data.storageType,
        wallColor: data.wallColor,
        floorColor: data.floorColor,
        wallTexture: null,
        floorTexture: null,
        lighting: false,
        transparency: false,
        animation: false,
        gridVisible: false,
        measureMode: false,
        sectionView: false,
        wireframe: false,
        annotations: [],
    };
    let history = [];
    let historyIndex = -1;
    let measurePoints = [];
    let measurements = [];
    let forklift;

    function saveState() {
        history = history.slice(0, historyIndex + 1);
        history.push(JSON.parse(JSON.stringify(state)));
        historyIndex++;
    }

    function undo() {
        if (historyIndex > 0) {
            historyIndex--;
            state = JSON.parse(JSON.stringify(history[historyIndex]));
            updateScene();
        }
    }

    function redo() {
        if (historyIndex < history.length - 1) {
            historyIndex++;
            state = JSON.parse(JSON.stringify(history[historyIndex]));
            updateScene();
        }
    }

    function updateScene() {
        scene.clear();

        const wallMaterial = new THREE.MeshPhongMaterial({
            color: state.wallColor,
            transparent: state.transparency,
            opacity: state.transparency ? 0.3 : 1,
            wireframe: state.wireframe,
            map: state.wallTexture,
        });
        const floorMaterial = new THREE.MeshPhongMaterial({
            color: state.floorColor,
            wireframe: state.wireframe,
            map: state.floorTexture,
        });

        // Floor
        const floor = new THREE.Mesh(
            new THREE.BoxGeometry(state.dimensions.length, 0.1, state.dimensions.width),
            floorMaterial
        );
        floor.position.y = state.dimensions.height / 2;
        scene.add(floor);

        // Walls
        const leftWall = new THREE.Mesh(
            new THREE.BoxGeometry(0.2, state.dimensions.height, state.dimensions.width),
            wallMaterial
        );
        leftWall.position.x = -state.dimensions.length / 2;
        scene.add(leftWall);

        const rightWall = new THREE.Mesh(
            new THREE.BoxGeometry(0.2, state.dimensions.height, state.dimensions.width),
            wallMaterial
        );
        rightWall.position.x = state.dimensions.length / 2;
        scene.add(rightWall);

        const backWall = new THREE.Mesh(
            new THREE.BoxGeometry(state.dimensions.length, state.dimensions.height, 0.2),
            wallMaterial
        );
        backWall.position.z = -state.dimensions.width / 2;
        scene.add(backWall);

        // Roof
        const roof = new THREE.Mesh(
            new THREE.BoxGeometry(state.dimensions.length, 0.2, state.dimensions.width),
            wallMaterial
        );
        roof.position.y = -state.dimensions.height / 2;
        scene.add(roof);

        // Storage Units
        const aisleWidth = state.dimensions.width / (state.aisles + 1);
        for (let i = 0; i < state.aisles; i++) {
            const xPos = -state.dimensions.length / 2 + (i + 1) * (state.dimensions.length / (state.aisles + 1));
            const shelfGroup = new THREE.Group();
            if (state.storageType === 'Metal Racks') {
                for (let level = 0; level < 5; level++) {
                    const shelf = new THREE.Mesh(
                        new THREE.BoxGeometry(1, state.dimensions.width * 0.7, 0.05),
                        new THREE.MeshPhongMaterial({ color: '#c0c0c0', wireframe: state.wireframe })
                    );
                    shelf.position.z = (level + 1) * state.dimensions.height / 6;
                    shelfGroup.add(shelf);
                }
            } else if (state.storageType === 'Pallets') {
                const crateCount = Math.floor(state.dimensions.length / 2);
                for (let j = 0; j < crateCount; j++) {
                    const crate = new THREE.Mesh(
                        new THREE.BoxGeometry(1.5, 0.8, 1.5),
                        new THREE.MeshPhongMaterial({ color: i % 2 === 0 ? '#00ff00' : '#ffa500', wireframe: state.wireframe })
                    );
                    crate.position.x = -state.dimensions.length * 0.4 + j * (state.dimensions.length * 0.8 / crateCount);
                    crate.position.y = state.dimensions.height / 2 - 0.4;
                    shelfGroup.add(crate);
                }
            }
            shelfGroup.position.x = xPos;
            scene.add(shelfGroup);
        }

        // Forklift
        forklift = new THREE.Group();
        const body = new THREE.Mesh(
            new THREE.BoxGeometry(1.5, 0.8, 0.8),
            new THREE.MeshPhongMaterial({ color: '#ffa500', wireframe: state.wireframe })
        );
        forklift.add(body);
        forklift.position.set(-state.dimensions.length / 3, state.dimensions.height / 2 - 0.4, state.dimensions.width / 3);
        scene.add(forklift);

        // Grid
        if (state.gridVisible) {
            const gridSize = Math.max(state.dimensions.length, state.dimensions.width);
            const grid = new THREE.GridHelper(gridSize, gridSize);
            grid.position.y = state.dimensions.height / 2 - 0.01;
            scene.add(grid);
        }

        // Section View
        if (state.sectionView) {
            const section = new THREE.Mesh(
                new THREE.PlaneGeometry(state.dimensions.length * 2, state.dimensions.width * 2),
                new THREE.MeshBasicMaterial({ color: '#00ffff', transparent: true, opacity: 0.3, side: THREE.DoubleSide })
            );
            section.position.y = state.dimensions.height / 4;
            section.rotation.x = Math.PI / 2;
            scene.add(section);
        }

        // Lighting
        if (state.lighting) {
            const pointLight = new THREE.PointLight(0xffffff, 0.9);
            pointLight.position.set(0, -state.dimensions.height / 2 + 0.1, 0);
            scene.add(pointLight);
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.4);
            scene.add(ambientLight);
        } else {
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
            scene.add(ambientLight);
        }

        // Annotations
        state.annotations.forEach((annotation) => {
            const spriteMaterial = new THREE.SpriteMaterial({
                map: (() => {
                    const canvas = document.createElement('canvas');
                    canvas.width = 256;
                    canvas.height = 64;
                    const ctx = canvas.getContext('2d');
                    ctx.fillStyle = 'rgba(0,0,0,0.7)';
                    ctx.fillRect(0, 0, 256, 64);
                    ctx.fillStyle = 'white';
                    ctx.font = '20px Arial';
                    ctx.fillText(annotation.text, 10, 40);
                    return new THREE.CanvasTexture(canvas);
                })(),
            });
            const sprite = new THREE.Sprite(spriteMaterial);
            sprite.position.set(annotation.x, annotation.y, annotation.z);
            sprite.scale.set(2, 0.5, 1);
            scene.add(sprite);
        });

        // Update Stats
        const volume = state.dimensions.length * state.dimensions.width * state.dimensions.height;
        const usedSpace = ((state.aisles * 1.5 * 0.8 * 1.5) / volume * 100).toFixed(2);
        document.getElementById('volume').textContent = `Volume: ${volume.toFixed(2)} m³`;
        document.getElementById('space-used').textContent = `Espace utilisé: ${usedSpace}%`;
        document.getElementById('measurements').textContent = `Mesures: ${measurements.map(m => m.toFixed(2) + 'm').join(', ')}`;
    }

    // Animation Loop
    function animate() {
        requestAnimationFrame(animate);
        if (state.animation && forklift) {
            forklift.position.x = -state.dimensions.length / 3 + Math.sin(Date.now() * 0.001) * (state.dimensions.length / 3);
        }
        renderer.render(scene, camera);
    }
    animate();

    // Event Listeners
    document.getElementById('lighting').addEventListener('change', (e) => {
        state.lighting = e.target.checked;
        saveState();
        updateScene();
    });

    document.getElementById('transparency').addEventListener('change', (e) => {
        state.transparency = e.target.checked;
        saveState();
        updateScene();
    });

    document.getElementById('animation').addEventListener('change', (e) => {
        state.animation = e.target.checked;
        saveState();
        updateScene();
    });

    document.getElementById('grid-toggle').addEventListener('click', () => {
        state.gridVisible = !state.gridVisible;
        saveState();
        updateScene();
    });

    document.getElementById('measure-toggle').addEventListener('click', () => {
        state.measureMode = !state.measureMode;
        saveState();
        updateScene();
    });

    document.getElementById('section-toggle').addEventListener('click', () => {
        state.sectionView = !state.sectionView;
        saveState();
        updateScene();
    });

    document.getElementById('wireframe-toggle').addEventListener('click', () => {
        state.wireframe = !state.wireframe;
        saveState();
        updateScene();
    });

    document.getElementById('add-annotation').addEventListener('click', () => {
        const text = document.getElementById('annotation-text').value;
        if (text) {
            state.annotations.push({ text, x: 0, y: state.dimensions.height / 2, z: 0 });
            document.getElementById('annotation-text').value = '';
            saveState();
            updateScene();
        }
    });

    document.getElementById('undo').addEventListener('click', undo);
    document.getElementById('redo').addEventListener('click', redo);

    document.getElementById('wall-texture').addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = () => {
                state.wallTexture = new THREE.TextureLoader().load(reader.result);
                saveState();
                updateScene();
            };
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('floor-texture').addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = () => {
                state.floorTexture = new THREE.TextureLoader().load(reader.result);
                saveState();
                updateScene();
            };
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('export-png').addEventListener('click', () => {
        renderer.render(scene, camera);
        const dataURL = renderer.domElement.toDataURL('image/png');
        const link = document.createElement('a');
        link.href = dataURL;
        link.download = 'entrepot_3d.png';
        link.click();
    });

    document.getElementById('export-pdf').addEventListener('click', () => {
        renderer.render(scene, camera);
        const dataURL = renderer.domElement.toDataURL('image/png');
        const pdf = new jsPDF();
        pdf.addImage(dataURL, 'PNG', 10, 10, 190, 100);
        pdf.text(`Entrepôt: ${state.dimensions.length.toFixed(2)}x${state.dimensions.width.toFixed(2)}x${state.dimensions.height.toFixed(2)}m`, 10, 120);
        pdf.text(`Allées: ${state.aisles}, Stockage: ${state.storageType}`, 10, 130);
        pdf.save('entrepot_3d.pdf');
    });

    document.getElementById('export-gltf').addEventListener('click', () => {
        const exporter = new GLTFExporter();
        exporter.parse(scene, (gltf) => {
            const output = JSON.stringify(gltf, null, 2);
            const blob = new Blob([output], { type: 'application/octet-stream' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'entrepot_3d.gltf';
            link.click();
        });
    });

    document.getElementById('theme-toggle').addEventListener('click', () => {
        const body = document.body;
        body.classList.toggle('dark');
        body.classList.toggle('bg-gray-100');
        body.classList.toggle('bg-gray-900');
        body.classList.toggle('text-white');
        document.getElementById('theme-toggle').textContent = body.classList.contains('dark') ? '☀️' : '🌙';
    });

    // Measurements
    const raycaster = new THREE.Raycaster();
    const mouse = new THREE.Vector2();
    canvas.addEventListener('click', (event) => {
        if (!state.measureMode) return;
        mouse.x = (event.offsetX / canvas.clientWidth) * 2 - 1;
        mouse.y = -(event.offsetY / canvas.clientHeight) * 2 + 1;
        raycaster.setFromCamera(mouse, camera);
        const intersects = raycaster.intersectObjects(scene.children, true);
        if (intersects.length > 0) {
            measurePoints.push(intersects[0].point);
            if (measurePoints.length === 2) {
                const distance = measurePoints[0].distanceTo(measurePoints[1]);
                measurements.push(distance);
                measurePoints = [];
                updateScene();
            }
        }
    });

    // Initial Camera Position
    camera.position.set(30, 15, 30);
    saveState();
    updateScene();
});