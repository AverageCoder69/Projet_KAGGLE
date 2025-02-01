var introMusic = document.getElementById('intro-music');

introMusic.addEventListener('canplaythrough', function() {
    introMusic.play();
});

document.getElementById('intro').addEventListener('click', function() {
    introMusic.pause();
    window.location.href = 'index.php'; // Remplacez 'index.php' par le nom de votre page principale
});
