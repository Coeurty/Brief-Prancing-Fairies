// Select the necessary elements
const carouselSlides = document.getElementById('carouselSlides');

// TODO: See for a better solution?
if (carouselSlides) {
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

// Declare the interval variable globally to reset it
let slideInterval;

// Function to reset the timer
function resetTimer() {
  // Clear the existing interval
  clearInterval(slideInterval);

  // Set a new interval for automatic slide change (after 6 seconds)
  slideInterval = setInterval(nextSlide, 6000);  // 6000 ms = 6 seconds
}

// Start the timer when the page loads
resetTimer();

// Trigger automatic slide change every 6 seconds
// Set up event listeners for the navigation buttons
document.getElementById('nextButton').addEventListener('click', function() {
  nextSlide();
  resetTimer();  // Reset the timer when clicking manually
});

document.getElementById('prevButton').addEventListener('click', function() {
  prevSlide();
  resetTimer();  // Reset the timer when clicking manually
});
}