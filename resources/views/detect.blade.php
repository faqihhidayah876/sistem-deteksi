<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deteksi Penyakit Daun Mangga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; }
        /* Kustomisasi warna pastel */
        .bg-pastel-green { background-color: #E8F5E9; } /* Hijau sangat muda */
        .btn-pastel { background-color: #81C784; color: white; transition: 0.3s; }
        .btn-pastel:hover { background-color: #66BB6A; }
    </style>
</head>
<body class="bg-pastel-green min-h-screen flex items-center justify-center p-4">

    <div class="bg-white rounded-3xl shadow-xl w-full max-w-4xl overflow-hidden flex flex-col md:flex-row">

        <div class="w-full md:w-1/2 bg-green-50 p-8 flex flex-col justify-center items-center text-center">
            <div class="bg-white p-4 rounded-full shadow-md mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">MangoDoctor</h1>
            <p class="text-gray-500 text-sm">Sistem Deteksi Dini Penyakit pada Daun Mangga berbasis AI.</p>

            <div class="mt-8 text-left w-full space-y-2 text-xs text-gray-400">
                <p>Created by: Mahasiswa SI Sem 3</p>
                <p>Powered by: Laravel & Teachable Machine</p>
            </div>
        </div>

        <div class="w-full md:w-1/2 p-8 bg-white">
            <h2 class="text-xl font-semibold text-gray-700 mb-6">Upload Foto Daun</h2>

            <div class="mb-6">
                <label for="imageUpload" class="flex flex-col items-center justify-center w-full h-48 border-2 border-green-300 border-dashed rounded-2xl cursor-pointer hover:bg-green-50 transition">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <svg class="w-10 h-10 mb-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Klik untuk upload</span> gambar daun</p>
                        <p class="text-xs text-gray-400">Format: JPG, PNG</p>
                    </div>
                    <input id="imageUpload" type="file" class="hidden" accept="image/*" onchange="previewImage(event)" />
                </label>
            </div>

            <div id="image-preview-container" class="hidden mb-6 text-center">
                <p class="text-sm text-gray-500 mb-2">Preview Gambar:</p>
                <img id="image-preview" class="max-h-48 mx-auto rounded-lg shadow-sm border border-gray-200" src="#" alt="Preview" />
            </div>

            <button type="button" onclick="predict()" class="w-full btn-pastel py-3 rounded-xl font-semibold shadow-lg transform active:scale-95 text-lg">
                Analisa Sekarang
            </button>

            <div id="loading" class="hidden mt-4 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-green-500"></div>
                <p class="text-green-600 text-sm mt-2">Sedang menganalisa AI...</p>
            </div>

            <div id="result-container" class="hidden mt-6 bg-green-50 p-4 rounded-xl border border-green-100">
                <h3 class="text-gray-700 font-bold">Hasil Diagnosa:</h3>
                <p id="label-result" class="text-2xl font-bold text-green-700 mt-1">-</p>
                <div class="w-full bg-gray-200 rounded-full h-2.5 mt-3">
                    <div id="confidence-bar" class="bg-green-500 h-2.5 rounded-full" style="width: 0%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1 text-right">Akurasi: <span id="confidence-text">0%</span></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>

    <script type="text/javascript">
        // --- KONFIGURASI ---
        // Ganti URL ini dengan link model kamu dari Teachable Machine
        const URL = "https://teachablemachine.withgoogle.com/models/SyPfyrCXm/";

        let model, labelContainer, maxPredictions;

        // Load Model saat halaman dibuka
        async function init() {
            const modelURL = URL + "model.json";
            const metadataURL = URL + "metadata.json";

            try {
                model = await tmImage.load(modelURL, metadataURL);
                maxPredictions = model.getTotalClasses();
                console.log("Model Loaded Successfully");
            } catch (error) {
                console.error("Gagal load model:", error);
                alert("Model gagal dimuat. Pastikan Link URL benar!");
            }
        }

        // Fungsi Preview Gambar
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function(){
                const output = document.getElementById('image-preview');
                output.src = reader.result;
                document.getElementById('image-preview-container').classList.remove('hidden');
                document.getElementById('result-container').classList.add('hidden'); // Sembunyikan hasil lama
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        // Fungsi Prediksi
        async function predict() {
            const imageElement = document.getElementById('image-preview');

            if (!imageElement.src || imageElement.src === "#") {
                alert("Silakan upload gambar daun mangga terlebih dahulu!");
                return;
            }

            // Tampilkan Loading
            document.getElementById('loading').classList.remove('hidden');
            document.getElementById('result-container').classList.add('hidden');

            // Prediksi
            const prediction = await model.predict(imageElement);

            // Cari probabilitas tertinggi
            let highestProb = 0;
            let bestClass = "";

            for (let i = 0; i < maxPredictions; i++) {
                if (prediction[i].probability > highestProb) {
                    highestProb = prediction[i].probability;
                    bestClass = prediction[i].className;
                }
            }

            // Tampilkan Hasil (Delay sedikit biar smooth)
            setTimeout(() => {
                document.getElementById('loading').classList.add('hidden');
                document.getElementById('result-container').classList.remove('hidden');

                document.getElementById('label-result').innerText = bestClass;
                const percentage = (highestProb * 100).toFixed(2);
                document.getElementById('confidence-text').innerText = percentage + "%";
                document.getElementById('confidence-bar').style.width = percentage + "%";
            }, 1000);
        }

        // Jalankan init
        init();
    </script>
</body>
</html>
