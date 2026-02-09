<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PatatDoctor AI - Deteksi Penyakit Daun Patat</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        body { font-family: 'Poppins', sans-serif; }
        .bg-gradient-pastel { background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 50%, #bbf7d0 100%); background-attachment: fixed; }
        .glass-card { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.6); }
        .scan-line { position: absolute; width: 100%; height: 3px; background: #10b981; box-shadow: 0 0 15px #10b981, 0 0 5px #fff; animation: scan 2.5s cubic-bezier(0.4, 0, 0.2, 1) infinite; z-index: 10; }
        @keyframes scan { 0% { top: 0%; opacity: 0; } 10% { opacity: 1; } 90% { opacity: 1; } 100% { top: 100%; opacity: 0; } }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="bg-gradient-pastel min-h-screen text-gray-700">

    <nav class="w-full py-4 px-6 fixed top-0 z-50 bg-white/60 backdrop-blur-md border-b border-white/30">
        <div class="flex items-center gap-3">
            <div class="bg-gradient-to-br from-emerald-400 to-green-600 text-white p-2 rounded-xl">
                <i class="fa-solid fa-leaf text-xl"></i>
            </div>
            <span class="text-xl font-bold text-emerald-900">Patat<span class="text-emerald-600">Doctor</span></span>
        </div>
    </nav>

    <main class="pt-24 pb-12 px-4 max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-4 space-y-6">
            <div class="glass-card rounded-3xl p-6 h-[420px] flex flex-col">
                <div class="flex justify-between items-center mb-4 border-b pb-2">
                    <h3 class="font-bold text-gray-700"><i class="fa-solid fa-clock-rotate-left text-emerald-500 mr-2"></i>Riwayat</h3>
                    <button onclick="clearHistory()" class="text-xs text-red-400 hover:text-red-600"><i class="fa-solid fa-trash"></i> Hapus</button>
                </div>
                <div id="history-list" class="flex-1 overflow-y-auto no-scrollbar space-y-3">
                    <p class="text-center text-gray-400 text-xs mt-10">Belum ada riwayat.</p>
                </div>
            </div>
        </div>

        <div class="lg:col-span-8">
            <div class="glass-card rounded-[2.5rem] p-6 relative overflow-hidden shadow-2xl border-t-4 border-emerald-400">
                <div class="flex justify-center space-x-2 mb-6 bg-gray-100/60 p-1.5 rounded-full max-w-sm mx-auto">
                    <button onclick="switchMode('upload')" id="tab-upload" class="flex-1 py-2 rounded-full text-sm font-bold bg-white text-emerald-600 shadow-sm">Upload</button>
                    <button onclick="switchMode('camera')" id="tab-camera" class="flex-1 py-2 rounded-full text-sm font-bold text-gray-500">Kamera</button>
                </div>

                <div class="relative min-h-[400px] flex flex-col items-center justify-center bg-white/50 rounded-3xl border border-white/60 p-4">
                    <div id="section-upload" class="w-full max-w-md">
                        <label class="flex flex-col items-center justify-center w-full h-80 border-2 border-emerald-300/50 border-dashed rounded-3xl cursor-pointer hover:bg-emerald-50/50 transition">
                            <i class="fa-solid fa-image text-4xl text-emerald-500 mb-4"></i>
                            <p class="font-bold text-gray-700">Pilih Gambar Daun</p>
                            <input id="imageUpload" type="file" class="hidden" accept="image/*" onchange="previewImage(event)" />
                        </label>
                    </div>

                    <div id="section-camera" class="w-full max-w-md hidden flex-col items-center">
                        <div class="relative w-full h-80 bg-gray-900 rounded-3xl overflow-hidden">
                            <video id="webcam" autoplay playsinline class="absolute inset-0 w-full h-full object-cover"></video>
                            <div id="scan-overlay" class="hidden absolute inset-0"><div class="scan-line"></div></div>
                            <div id="camera-placeholder" class="absolute inset-0 flex items-center justify-center text-white/50"><p>Kamera Mati</p></div>
                        </div>
                        <div class="flex gap-3 mt-4 w-full">
                            <button onclick="startCamera()" class="flex-1 py-3 bg-white border rounded-xl font-bold">Start</button>
                            <button onclick="captureImage()" id="btn-capture" disabled class="flex-1 py-3 bg-emerald-500 text-white rounded-xl font-bold disabled:opacity-50">Capture</button>
                        </div>
                    </div>

                    <div id="result-overlay" class="hidden absolute inset-0 bg-white z-20 flex flex-col animate__animated animate__fadeInUp rounded-3xl">
                        <div class="p-4 border-b flex justify-between items-center">
                            <h3 class="font-bold">Hasil Analisa</h3>
                            <button onclick="closeResult()"><i class="fa-solid fa-xmark text-xl"></i></button>
                        </div>
                        <div class="p-6 flex-1 overflow-y-auto">
                            <img id="result-image" class="w-32 h-32 rounded-xl object-cover mb-4 shadow-md mx-auto">
                            <div class="text-center mb-4">
                                <span id="result-badge" class="px-3 py-1 rounded-full text-xs font-bold text-white bg-gray-400">LOADING...</span>
                                <h2 id="result-title" class="text-2xl font-bold text-gray-800 mt-2">...</h2>
                                <p id="result-confidence" class="text-gray-400 text-sm">0%</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl border">
                                <p id="result-desc" class="text-sm text-gray-700">...</p>
                            </div>
                        </div>
                        <div class="p-4 border-t">
                            <button onclick="closeResult()" class="w-full py-3 bg-gray-800 text-white rounded-xl font-bold">Scan Lagi</button>
                        </div>
                    </div>

                    <div id="loading" class="hidden absolute inset-0 bg-white/90 z-10 flex flex-col items-center justify-center rounded-3xl">
                        <i class="fa-solid fa-circle-notch fa-spin text-4xl text-emerald-500 mb-3"></i>
                        <p class="font-bold text-emerald-800">Menganalisa...</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <canvas id="canvas-capture" class="hidden"></canvas>

    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>

    <script>
        const URL_MODEL = "https://teachablemachine.withgoogle.com/models/CONTOH_LINK_KAMU/";
        let model, maxPredictions, webcamStream;

        const solutions = {
            "sehat": "Daun Patat sehat. Pertahankan perawatan.",
            "sakit": "Daun Patat sakit. Segera isolasi dan obati.",
            "bukan daun": "Objek tidak dikenali sebagai daun patat.",
            "default": "Tidak yakin. Coba foto ulang."
        };

        async function init() {
            renderHistory();
            try {
                const modelURL = URL_MODEL + "model.json";
                const metadataURL = URL_MODEL + "metadata.json";
                model = await tmImage.load(modelURL, metadataURL);
                maxPredictions = model.getTotalClasses();
            } catch(e) { alert("Error loading model"); }
        }

        // Logic UI Switching
        function switchMode(mode) {
            document.getElementById('section-upload').classList.toggle('hidden', mode !== 'upload');
            document.getElementById('section-camera').classList.toggle('hidden', mode !== 'camera');
            document.getElementById('section-camera').style.display = mode === 'camera' ? 'flex' : 'none';
            if(mode === 'upload') stopCamera();

            // Toggle Button Styles
            const btnUpload = document.getElementById('tab-upload');
            const btnCamera = document.getElementById('tab-camera');
            if(mode === 'upload'){
                btnUpload.className = "flex-1 py-2 rounded-full text-sm font-bold bg-white text-emerald-600 shadow-sm";
                btnCamera.className = "flex-1 py-2 rounded-full text-sm font-bold text-gray-500";
            } else {
                btnCamera.className = "flex-1 py-2 rounded-full text-sm font-bold bg-white text-emerald-600 shadow-sm";
                btnUpload.className = "flex-1 py-2 rounded-full text-sm font-bold text-gray-500";
            }
        }

        // Camera Logic
        async function startCamera() {
            const video = document.getElementById('webcam');
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) return alert("No camera support");
            try {
                webcamStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } });
                video.srcObject = webcamStream;
                video.onloadedmetadata = () => {
                    video.play();
                    document.getElementById('camera-placeholder').classList.add('hidden');
                    document.getElementById('scan-overlay').classList.remove('hidden');
                    document.getElementById('btn-capture').disabled = false;
                };
            } catch(e) { alert("Camera error"); }
        }

        function stopCamera() {
            if(webcamStream) { webcamStream.getTracks().forEach(t=>t.stop()); webcamStream = null; }
            document.getElementById('camera-placeholder').classList.remove('hidden');
            document.getElementById('scan-overlay').classList.add('hidden');
            document.getElementById('btn-capture').disabled = true;
        }

        async function captureImage() {
            const video = document.getElementById('webcam');
            const canvas = document.getElementById('canvas-capture');
            canvas.width = video.videoWidth; canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            const imgData = canvas.toDataURL('image/png');
            showResult(imgData); predict(canvas, imgData);
        }

        function previewImage(e) {
            const reader = new FileReader();
            reader.onload = function() {
                const img = new Image(); img.src = reader.result;
                img.onload = () => { showResult(reader.result); predict(img, reader.result); }
            }
            if(e.target.files[0]) reader.readAsDataURL(e.target.files[0]);
        }

        async function predict(input, imgData) {
            document.getElementById('loading').classList.remove('hidden');
            if(!model) await init();
            const prediction = await model.predict(input);
            let highest = 0; let best = "";
            for(let i=0; i<maxPredictions; i++){
                if(prediction[i].probability > highest){ highest = prediction[i].probability; best = prediction[i].className; }
            }
            setTimeout(() => {
                updateUI(best, highest);
                saveHistory(best, highest, imgData);
                document.getElementById('loading').classList.add('hidden');
            }, 800);
        }

        function showResult(src) {
            document.getElementById('result-image').src = src;
            document.getElementById('result-overlay').classList.remove('hidden');
        }
        function closeResult() { document.getElementById('result-overlay').classList.add('hidden'); }

        function updateUI(label, prob) {
            const elTitle = document.getElementById('result-title');
            const elBadge = document.getElementById('result-badge');
            const elDesc = document.getElementById('result-desc');
            const elConf = document.getElementById('result-confidence');

            elTitle.innerText = label;
            elConf.innerText = (prob*100).toFixed(0) + "%";

            const lower = label.toLowerCase();
            if(lower.includes('sehat')) {
                elBadge.className = "px-3 py-1 rounded-full text-xs font-bold text-white bg-emerald-500";
                elDesc.innerText = solutions['sehat'];
            } else if(lower.includes('sakit')) {
                elBadge.className = "px-3 py-1 rounded-full text-xs font-bold text-white bg-rose-500";
                elDesc.innerText = solutions['sakit'];
            } else if(lower.includes('bukan daun')) {
                elBadge.className = "px-3 py-1 rounded-full text-xs font-bold text-white bg-slate-500";
                elDesc.innerText = solutions['bukan daun'];
            } else {
                elBadge.className = "px-3 py-1 rounded-full text-xs font-bold text-white bg-gray-400";
                elDesc.innerText = solutions['default'];
            }
        }

        function saveHistory(label, prob, img) {
            const item = { label, prob, img, date: new Date().toLocaleTimeString('id-ID'), id: Date.now() };
            let history = JSON.parse(localStorage.getItem('patatHistory')) || [];
            history.unshift(item); if(history.length > 5) history.pop();
            localStorage.setItem('patatHistory', JSON.stringify(history));
            renderHistory();
        }

        function renderHistory() {
            const list = document.getElementById('history-list');
            const history = JSON.parse(localStorage.getItem('patatHistory')) || [];
            if(history.length === 0) return list.innerHTML = '<p class="text-center text-gray-400 text-xs mt-10">Belum ada riwayat.</p>';

            let html = '';
            history.forEach(item => {
                const lower = item.label.toLowerCase();
                let color = "bg-gray-100 text-gray-600";
                if(lower.includes('sehat')) color = "bg-emerald-100 text-emerald-600";
                if(lower.includes('sakit')) color = "bg-rose-100 text-rose-600";
                if(lower.includes('bukan daun')) color = "bg-slate-100 text-slate-600";

                html += `<div class="flex items-center gap-3 p-3 bg-white rounded-xl border shadow-sm">
                    <img src="${item.img}" class="w-12 h-12 rounded-lg object-cover">
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-sm truncate text-gray-700">${item.label}</h4>
                        <p class="text-[10px] text-gray-400">${item.date}</p>
                    </div>
                    <span class="px-2 py-1 rounded text-[10px] font-bold ${color}">${(item.prob*100).toFixed(0)}%</span>
                </div>`;
            });
            list.innerHTML = html;
        }
        function clearHistory() { localStorage.removeItem('patatHistory'); renderHistory(); }

        init();
    </script>
</body>
</html>
