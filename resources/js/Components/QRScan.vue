<script setup>
import { ref, nextTick, watch } from "vue";
import { QrcodeStream } from "vue-qrcode-reader";

const qrCodeResult = ref(null);
const scannedCodes = ref([]);
const isScanning = ref(true);
const lastFrame = ref(null);
const lastFrameCanvas = ref(null);
const detectedBounds = ref([]);

// Capture detected QR code
const onDetect = (detectedCodes) => {
  if (detectedCodes.length > 0) {
    qrCodeResult.value = detectedCodes[0].rawValue;
    scannedCodes.value.push(qrCodeResult.value);
    isScanning.value = false; // Stop scanning after a successful scan
  }
};

// Save last frame from the video feed
const onDecode = ({ imageData, detectedCodes }) => {
  if (imageData) {
    lastFrame.value = imageData; // Store last captured frame
    detectedBounds.value = detectedCodes.map(code => code.boundingBox);
  }
};

// Watch `isScanning` and update the canvas when it stops
watch(isScanning, async (newValue) => {
  if (!newValue && lastFrame.value && lastFrameCanvas.value) {
    await nextTick(); // Ensure canvas is rendered first
    const canvas = lastFrameCanvas.value;
    const ctx = canvas.getContext("2d");

    // Ensure `imageData` is valid
    if (lastFrame.value && lastFrame.value.width > 0 && lastFrame.value.height > 0) {
      // Set canvas dimensions exactly to match the captured frame
      canvas.width = lastFrame.value.width;
      canvas.height = lastFrame.value.height;

      // Draw the image onto the canvas
      ctx.putImageData(lastFrame.value, 0, 0);
    }
  }
});

// Reset the scanner
const scanAgain = () => {
  qrCodeResult.value = null;
  isScanning.value = true; // Re-enable scanner
  detectedBounds.value = [];
};
</script>

<template>
  <div class="p-4 border rounded shadow-md">
    <h2 class="text-lg font-semibold mb-2">Scan QR Code</h2>

    <div class="relative w-full max-w-md mx-auto">
      <!-- QR Code Scanner -->
      <QrcodeStream 
        v-if="isScanning" 
        @detect="onDetect" 
        @decode="onDecode" 
        class="border rounded-md w-full h-auto max-h-64" 
      />
      
      <!-- Canvas to Display Last Frame -->
      <canvas 
        v-else 
        ref="lastFrameCanvas" 
        class="border rounded-md w-full max-h-64" 
      />

      <!-- Outline detected QR Codes -->
      <div v-if="detectedBounds.length && isScanning" class="absolute top-0 left-0 w-full h-full">
        <div v-for="(box, index) in detectedBounds" :key="index" class="absolute border-2 border-red-500"
             :style="{ top: `${box.y}px`, left: `${box.x}px`, width: `${box.width}px`, height: `${box.height}px` }">
        </div>
      </div>
    </div>

    <div v-if="qrCodeResult" class="mt-4 p-2 bg-green-100 border border-green-400 rounded">
      <p>Scanned Code:</p>
      <p class="font-bold">{{ qrCodeResult }}</p>
    </div>

    <button v-if="!isScanning" @click="scanAgain" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded">
      Scan Again
    </button>

    <div v-if="scannedCodes.length" class="mt-4">
      <h3 class="text-lg font-semibold">Scanned Codes:</h3>
      <ul class="list-disc pl-5">
        <li v-for="(code, index) in scannedCodes" :key="index" class="mt-1">{{ code }}</li>
      </ul>
    </div>
  </div>
</template>

<style scoped>
@media (min-width: 768px) {
  .max-w-md {
    max-width: 400px;
  }
}
</style>
