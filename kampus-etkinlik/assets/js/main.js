document.addEventListener("DOMContentLoaded", function() {
    // Başvuru formunu bul
    const applyForm = document.getElementById('applyForm');

    // Eğer sayfada form varsa kontrol et
    if (applyForm) {
        applyForm.addEventListener('submit', function(event) {
            const noteInput = document.getElementById('user_note');
            
            // Not alanı boş bırakılmışsa formun gönderilmesini engelle
            if (noteInput.value.trim() === "") {
                event.preventDefault(); 
                alert("Lütfen başvuru nedeninizi veya notunuzu kısaca yazınız!");
            }
        });
    }
});