/**
 * Deteksi keberadaan wajah untuk swafoto absen (BUKAN pengenalan/pencocokan wajah).
 * MediaPipe Face Detector (Google) berjalan di perangkat; model ±230 KB dimuat sekali.
 * Bila model gagal dimuat (offline/perangkat lama) → tersedia=false, absen tetap bisa dilakukan.
 *
 *   const berhenti = window.siatuPantauWajah(videoEl, ({ tersedia, wajah }) => { ... });
 *   berhenti();
 */
const VERSI = '0.10.14';
const CDN = `https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@${VERSI}`;
const MODEL = 'https://storage.googleapis.com/mediapipe-models/face_detector/blaze_face_short_range/float16/1/blaze_face_short_range.tflite';

let janji = null;

function siapkanDetektor() {
    janji ??= (async () => {
        try {
            const { FaceDetector, FilesetResolver } = await import(/* @vite-ignore */ `${CDN}/vision_bundle.mjs`);
            const vision = await FilesetResolver.forVisionTasks(`${CDN}/wasm`);
            for (const delegate of ['GPU', 'CPU']) {
                try {
                    return await FaceDetector.createFromOptions(vision, {
                        baseOptions: { modelAssetPath: MODEL, delegate },
                        runningMode: 'VIDEO',
                        minDetectionConfidence: 0.6,
                    });
                } catch (e) { /* coba delegate berikutnya */ }
            }
        } catch (e) {
            console.warn('[SIATU] Deteksi wajah tidak tersedia:', e);
        }
        return null;
    })();
    return janji;
}

window.siatuPantauWajah = function (video, onUbah) {
    let aktif = true;
    let waktuTerakhir = -1;

    siapkanDetektor().then((detektor) => {
        if (!aktif) return;
        if (!detektor) return onUbah({ tersedia: false, wajah: null });

        const putaran = () => {
            if (!aktif) return;
            if (video.readyState >= 2 && video.currentTime !== waktuTerakhir) {
                waktuTerakhir = video.currentTime;
                try {
                    onUbah({ tersedia: true, wajah: detektor.detectForVideo(video, performance.now()).detections.length });
                } catch (e) { /* frame belum siap */ }
            }
            setTimeout(putaran, 250); // ±4x per detik: cukup responsif, hemat baterai
        };
        putaran();
    });

    return () => { aktif = false; };
};
