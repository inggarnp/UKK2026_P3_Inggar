document.addEventListener("DOMContentLoaded", () => {

  /* ======================
     STEP CONTROL
  ====================== */
  const steps = ["a", "b", "c", "d"];

  const showStep = (active) => {
    steps.forEach(s => {
      const stepEl = document.getElementById(`step-${s}`);
      if (!stepEl) return;

      const isActive = s === active;
      stepEl.style.display = isActive ? "block" : "none";
      toggleStepInputs(s, isActive);
    });
  };

  const toggleStepInputs = (step, enable) => {
    const el = document.getElementById(`step-${step}`);
    if (!el) return;

    el.querySelectorAll("input, select, textarea").forEach(i => {
      i.disabled = !enable;
    });
  };


  /* ======================
     LIVE PREVIEW TEXT
  ====================== */
  const bindText = (inputId, previewId) => {
    const i = document.getElementById(inputId);
    const p = document.getElementById(previewId);
    if (!i || !p) return;

    i.addEventListener("input", () => {
      // Cek apakah input adalah textarea untuk mendukung multi-line
      if (i.tagName === 'TEXTAREA') {
        // Ganti \n dengan <br> untuk line breaks
        p.innerHTML = (i.value || "-").replace(/\n/g, '<br>');
      } else {
        p.textContent = i.value || "-";
      }
    });
  };

  [
    ["tanggal", "p-tanggal"],
    ["subject", "p-subject"],
    ["penerima", "p-penerima"],
    ["p1", "p-p1"],
    ["p2", "p-p2"],
    ["p3", "p-p3"],
    ["nama_penyusun", "p-nama_penyusun"]
  ].forEach(([i, p]) => bindText(i, p));

  /* ======================
     LOGO PREVIEW
  ====================== */
  const logo = document.getElementById("logo");
  const logoPrev = document.getElementById("p-logo");

  logo.addEventListener("change", () => {
    const file = logo.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = e => {
      logoPrev.src = e.target.result;
      logoPrev.style.display = "block";
    };
    reader.readAsDataURL(file);
  });

  /* ======================
     INIT
  ====================== */
  showStep("a");
  lockStepAInputs();

});


// panggil
previewLampiran("lkpd", "p-lkpd", "p-lkpd-img");
previewLampiran("materi", "p-materi", "p-materi-img");
previewLampiran("asesmen", "p-asesmen", "p-asesmen-img");


/* ======================
   BUTTON ACTIONS
====================== */
// Print Button
document.getElementById("btnPrint").addEventListener("click", () => {
  window.print();
});

document.getElementById("btnClear").addEventListener("click", () => {
  // Konfirmasi sebelum clear
  if (confirm("Apakah Anda yakin ingin menghapus semua data?")) {
    // Clear semua input
    document.querySelectorAll("input[type='text']").forEach(input => {
      input.value = "";
    });
    
    // Clear semua textarea
    document.querySelectorAll("textarea").forEach(textarea => {
      textarea.value = "";
    });
    
    // Reset preview ke default
    document.getElementById("p-tanggal").textContent = "Bandung, 30 Januari 2026";
    document.getElementById("p-subject").textContent = " Subject Job Applicatian";
    document.getElementById("p-penerima").innerHTML = "HRD PT. Budiman<br>Jl. Sudirman No 123<br>Jakarta Pusat";
    document.getElementById("p-p1").textContent = "dengan hormat saya membuat surat lamaran ini.";
    document.getElementById("p-p2").textContent = "dengan surat ini saya ingin melamar di divisi.";
    document.getElementById("p-p3").textContent = "semoga saya bisa di terima disini.";
    document.getElementById("p-nama_penyusun").textContent = "Inggar Nugraha P";
    
    alert("Data berhasil dihapus!");
  }
});

document.getElementById("btnSimpan").addEventListener("click", () => {
  const data = {
    tanggal: document.getElementById("tanggal").value,
    subject: document.getElementById("subject").value,
    penerima: document.getElementById("penerima").value,
    p1: document.getElementById("p1").value,
    p2: document.getElementById("p2").value,
    p3: document.getElementById("p3").value,
    nama_penyusun: document.getElementById("nama_penyusun").value
  };
  
  localStorage.setItem("suratLamaran", JSON.stringify(data));
  alert("Data berhasil disimpan!");
});

window.addEventListener("DOMContentLoaded", () => {
  const savedData = localStorage.getItem("suratLamaran");
  if (savedData) {
    const data = JSON.parse(savedData);
    
    if (data.tanggal) document.getElementById("tanggal").value = data.tanggal;
    if (data.subject) document.getElementById("subject").value = data.subject;
    if (data.penerima) document.getElementById("penerima").value = data.penerima;
    if (data.p1) document.getElementById("p1").value = data.p1;
    if (data.p2) document.getElementById("p2").value = data.p2;
    if (data.p3) document.getElementById("p3").value = data.p3;
    if (data.nama_penyusun) document.getElementById("nama_penyusun").value = data.nama_penyusun;
    
    document.querySelectorAll("input, textarea").forEach(el => {
      el.dispatchEvent(new Event("input"));
    });
  }
});