 // Select the necessary elements
 const carouselSlides = document.getElementById('carouselSlides');
 const slides = carouselSlides.children;
 let currentIndex = 0;

 // Function to move to the next slide
 function nextSlide() {
   currentIndex = (currentIndex + 1) % slides.length;  // Move to the next slide (loop to the start)
   carouselSlides.style.transform = `translateX(-${currentIndex * 100}%)`;  // Move the carousel
 }

 // Function to move to the previous slide
 function prevSlide() {
   currentIndex = (currentIndex - 1 + slides.length) % slides.length;  // Move to the previous slide (loop to the end)
   carouselSlides.style.transform = `translateX(-${currentIndex * 100}%)`;  // Move the carousel
 }

 // Trigger automatic slide change every 6 seconds
 setInterval(nextSlide, 8000);  // 8000 ms = 8 seconds

 // Set up event listeners for the navigation buttons
 document.getElementById('nextButton').addEventListener('click', nextSlide);
 document.getElementById('prevButton').addEventListener('click', prevSlide);