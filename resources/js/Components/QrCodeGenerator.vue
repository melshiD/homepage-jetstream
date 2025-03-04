<template>
    <div>
      <h2>QR Code Generator</h2>
  
      <label for="qrInput">Enter Text:</label>
      <input id="qrInput" v-model="qrCodeText" type="text" placeholder="Enter QR Code text" />
      
      <label for="qrSize">QR Code Size:</label>
      <input id="qrSize" v-model.number="qrSize" type="number" min="2" max="10" />
      
      <button @click="generateQRCode">Generate QR</button>
      <button @click="castCode">Cast Code</button>
  
      <div v-html="qrSvg"></div>
      
      <h3>Gallery</h3>
      <div class="gallery">
        <div v-for="(item, index) in gallery" :key="index" class="gallery-item">
          <p>{{ item.date }}</p>
          <div v-html="item.qrSvg" class="qr-thumbnail"></div>
          <p>{{ item.text.substring(0, 50) }}...</p>
          <button @click="downloadQRCode(item.qrSvg, index, 'svg')">SVG</button>
          <button @click="downloadQRCode(item.qrSvg, index, 'png')">PNG</button>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import QRCode from "qrcode-generator";
  
  export default {
    data() {
      return {
        qrCodeText: "Hello, QR Code!",
        qrSvg: "",
        qrSize: 6, // Default size
        gallery: []
      };
    },
    mounted() {
      this.generateQRCode();
    },
    methods: {
      generateQRCode() {
        if (!this.qrCodeText.trim()) return;
        const qr = QRCode(0, "L"); // Use version 0 and low error correction
        qr.addData(this.qrCodeText);
        qr.make();
        this.qrSvg = qr.createSvgTag(this.qrSize, 0); // Dynamic size
      },
      castCode() {
        if (!this.qrCodeText.trim()) return; // Prevent empty casts
        
        this.gallery.unshift({
          text: this.qrCodeText,
          qrSvg: this.qrSvg,
          date: new Date().toLocaleString()
        });
        
        this.qrCodeText = ""; // Reset input
        this.generateQRCode(); // Update QR code after reset
      },
      downloadQRCode(qrSvg, index, format) {
        if (format === 'svg') {
          const blob = new Blob([qrSvg], { type: "image/svg+xml" });
          const url = URL.createObjectURL(blob);
          const a = document.createElement("a");
          a.href = url;
          a.download = `qr_code_${index}.svg`;
          document.body.appendChild(a);
          a.click();
          document.body.removeChild(a);
          URL.revokeObjectURL(url);
        } else if (format === 'png') {
          const canvas = document.createElement("canvas");
          const ctx = canvas.getContext("2d");
          const img = new Image();
          img.src = 'data:image/svg+xml;base64,' + btoa(qrSvg);
          img.onload = () => {
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);
            const link = document.createElement("a");
            link.href = canvas.toDataURL("image/png");
            link.download = `qr_code_${index}.png`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
          };
        }
      }
    },
    watch: {
      qrCodeText() {
        this.generateQRCode();
      },
      qrSize() {
        this.generateQRCode();
      }
    }
  };
  </script>
  
  <style scoped>
  input {
    width: 100%;
    padding: 8px;
    margin: 10px 0;
    font-size: 16px;
  }
  button {
    padding: 8px 12px;
    margin: 5px;
    cursor: pointer;
  }
  .gallery {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }
  .gallery-item {
    border: 1px solid #ccc;
    padding: 10px;
    border-radius: 5px;
    text-align: center;
    width: auto;
  }
  .qr-thumbnail {
    width: auto;
    height: auto;
    overflow: hidden;
  }
  </style>
  