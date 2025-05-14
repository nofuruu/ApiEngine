const Toast = Swal.mixin({
    toast: true,
    position: "top-start", // Lebih alami untuk notifikasi non-interupsi
    showConfirmButton: false,
    showCloseButton: true,
    background: "#1f2937", // Warna abu gelap (Tailwind gray-800)
    color: "#ffffff", // Putih abu muda (Tailwind gray-50)
    timer: 1000,
    timerProgressBar: true,
    iconColor: "#0044ff", // Warna kuning default (Tailwind yellow-400)
    customClass: {
        popup: "rounded-xl shadow-md p-4 border border-gray-700",
    },
    showClass: {
        popup: "animate__animated animate__fadeInUp",
    },
    hideClass: {
        popup: "animate__animated animate__fadeOutDown",
    },
    didOpen: (toast) => {
        toast.addEventListener("mouseenter", Swal.stopTimer);
        toast.addEventListener("mouseleave", Swal.resumeTimer);
    },
});

function notify(type = "info", message = "Something happened") {
    const iconColors = {
        success: "#ffffff", // Tailwind green-500
        error: "#ffffff", // Tailwind red-500
        warning: "#f59e0b", // Tailwind amber-500
        info: "#3b82f6", // Tailwind blue-500
    };

    const backgroundColors = {
        success: "#16a34a", // Tailwind green-700
        error: "#dc2626", // Tailwind red-700
        warning: "#f59e0b", // Tailwind amber-500
        info: "#3b82f6", // Tailwind blue-500
    };

    const iconColor = iconColors[type] || "#3b82f6";
    const backgroundColor = backgroundColors[type] || "#1f2937"; // Default background for 'info'

    // Menampilkan toast sesuai dengan tipe
    Toast.fire({
        icon: type,
        title: message,
        iconColor: iconColor,
        background: backgroundColor, // Menyesuaikan background berdasarkan tipe
        timer: 1500,
    });
}
