
// Room filtering functionality//
const filterBtns = document.querySelectorAll('.filter-btn');
const roomCards = document.querySelectorAll('.room-card-detailed');

filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        // Remove active class from all buttons
        filterBtns.forEach(b => b.classList.remove('active'));
        // Add active class to clicked button
        btn.classList.add('active');

        const filter = btn.getAttribute('data-filter');

        roomCards.forEach(card => {
            if (filter === 'all' || card.getAttribute('data-category') === filter) {
                card.style.display = 'block';
                card.classList.add('fade-in');
            } else {
                card.style.display = 'none';
            }
        });
    });
});

// Book now functionality
document.querySelectorAll('.book-now').forEach(btn => {
    btn.addEventListener('click', (e) => {
        const roomType = e.target.getAttribute('data-room');
        const roomPrice = e.target.getAttribute('data-price');

        // Store room details in localStorage for booking form
        localStorage.setItem('selectedRoom', roomType);
        localStorage.setItem('selectedPrice', roomPrice);

        // Redirect to booking page
        window.location.href = 'booking.php';
    });
});

// Image thumbnail functionality
document.querySelectorAll('.thumbnail').forEach(thumb => {
    thumb.addEventListener('click', (e) => {
        const parentCard = e.target.closest('.room-card-detailed');
        const mainImage = parentCard.querySelector('.main-image');
        const thumbnails = parentCard.querySelectorAll('.thumbnail');

        // Remove active class from all thumbnails
        thumbnails.forEach(t => t.classList.remove('active'));
        // Add active class to clicked thumbnail
        e.target.classList.add('active');

        // Update main image
        const newImageUrl = e.target.style.backgroundImage;
        mainImage.style.backgroundImage = newImageUrl;
    });
});


// DOM Content Loaded Event//
document.addEventListener('DOMContentLoaded', function () {
    initializeNavigation();
    initializeRoomFilters();
    initializeRoomModals();
    initializeBookingSystem();
    initializeImageGallery();
    initializeScrollEffects();
    initializeContactForm();
    initializeSearchFunctionality();
    initializeCarousel();
});

// Navigation System//
function initializeNavigation() {
    const navMenu = document.getElementById('nav-menu');
    const navbar = document.getElementById('navbar');
    const header = document.getElementsByClassName('header');
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');


    // Mobile menu toggle
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function () {
            navMenu.classList.toggle('active');
            hamburger.classList.toggle('active');
        });

        // Close mobile menu when clicking on a link
        const navLinks = navMenu.querySelectorAll('a');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
                hamburger.classList.remove('active');
            });
        });
    }

    // Navbar scroll effect to make it sticky-->
    if (header) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }

    // Smooth scrolling 
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Room Filtering System//
function initializeRoomFilters() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const roomCards = document.querySelectorAll('.room-card-detailed');

    filterButtons.forEach(button => {
        button.addEventListener('click', function () {
            const filter = this.getAttribute('data-filter');

            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            // Filter rooms with animation
            roomCards.forEach(card => {
                const category = card.getAttribute('data-category');

                if (filter === 'all' || category === filter) {
                    card.style.display = 'block';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 100);
                } else {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 300);
                }
            });
        });
    });
}

// Room Modal System //
function initializeRoomModals() {
    const viewDetailsButtons = document.querySelectorAll('.view-details');
    const modal = document.getElementById('roomModal');
    const closeModal = document.querySelector('.close-modal');

    const roomData = {
        standard: {
            title: 'Standard Room',
            price: '₹999/night',
            description: 'Our Standard Rooms offer the perfect blend of comfort and style. Each room is thoughtfully designed with modern amenities and elegant furnishings to ensure a pleasant stay. Features include premium bedding, work desk, and beautiful city or garden views.',
            amenities: ['Queen Bed', '2 Guests', '30 m²', 'Free WiFi', 'Smart TV', 'Air Conditioning', 'Mini Bar', 'Room Service', 'Daily Housekeeping'],
            images: ['https://plus.unsplash.com/premium_photo-1745337150305-a7fadd0fa1f9?q=80&w=2073&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?q=80&w=2071&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'https://images.unsplash.com/photo-1648383228240-6ed939727ad6?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'https://images.unsplash.com/photo-1564329471042-7b3bfa3c51c1?q=80&w=2018&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D']
        },
        deluxe: {
            title: 'Deluxe Suite',
            price: '₹1999/night',
            description: 'Experience luxury in our Deluxe Suites featuring separate living areas, premium amenities, and stunning city views. Perfect for business travelers and romantic getaways with spacious layouts and premium furnishings.',
            amenities: ['King Bed', '3 Guests', '45 m²', 'Living Area', 'Luxury Bathroom', 'City View', 'Premium WiFi', 'Smart TV', 'Mini Kitchen'],
            images: ['https://images.pexels.com/photos/164595/pexels-photo-164595.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2', 'https://images.pexels.com/photos/2889618/pexels-photo-2889618.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2', 'https://plus.unsplash.com/premium_photo-1663091257768-8f089bf6b4fa?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'https://images.unsplash.com/photo-1725623831897-fb009acfe742?q=80&w=2075&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D']
        },
        presidential: {
            title: 'Presidential Suite',
            price: '₹1299/night',
            description: 'The epitome of luxury and sophistication. Our Presidential Suite offers unparalleled comfort with panoramic city views, premium amenities, and exclusive concierge services. Perfect for VIP guests and special occasions.',
            amenities: ['King Bed', '4 Guests', '80 m²', 'Kitchenette', 'Jacuzzi', 'Butler Service', 'Private Balcony', 'Premium Bar', 'Dining Area'],
            images: ['https://images.unsplash.com/photo-1702675301342-cac2dc3ef15a?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D', 'https://images.pexels.com/photos/2134224/pexels-photo-2134224.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2', 'https://images.pexels.com/photos/3316922/pexels-photo-3316922.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2', 'https://images.unsplash.com/photo-1582484983984-1a930896da01?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D']
        },
        executive: {
            title: 'Executive Room',
            price: '₹2999/night',
            description: 'Designed for business travelers, our Executive Rooms feature a dedicated workspace, premium amenities, and access to the executive lounge. Combines comfort with functionality for the modern business traveler.',
            amenities: ['Queen Bed', '2 Guests', '35 m²', 'Work Desk', 'Lounge Access', 'Daily Newspaper', 'Express Check-in', 'Business Center Access', 'Meeting Room Access'],
            images: ['https://images.pexels.com/photos/189333/pexels-photo-189333.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2', 'https://images.pexels.com/photos/2506990/pexels-photo-2506990.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2', 'https://images.unsplash.com/photo-1725962269029-e845b85e5ebf?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D0', 'https://images.unsplash.com/photo-1735385262843-3f0e0450b515?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D']
        }
    };

    viewDetailsButtons.forEach(button => {
        button.addEventListener('click', function () {
            const roomType = this.getAttribute('data-room');
            const room = roomData[roomType];

            if (room && modal) {
                populateModal(room);
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }
        });
    });

    if (closeModal && modal) {
        closeModal.addEventListener('click', closeRoomModal);
    }

    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeRoomModal();
            }
        });
    }

    // ESC key feature
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal && modal.style.display === 'block') {
            closeRoomModal();
        }
    });

    function populateModal(room) {
        document.getElementById('modalRoomTitle').textContent = room.title;
        document.getElementById('modalRoomPrice').textContent = room.price;
        document.getElementById('modalRoomDescription').textContent = room.description;

        // main image
        const mainImage = document.getElementById('modalMainImage');
        mainImage.style.backgroundImage = `url('${room.images[0]}')`;

        // thumbnails
        const thumbnailsContainer = document.getElementById('modalThumbnails');
        thumbnailsContainer.innerHTML = '';
        room.images.forEach((image, index) => {
            const thumbnail = document.createElement('div');
            thumbnail.className = `modal-thumbnail ${index === 0 ? 'active' : ''}`;
            thumbnail.style.backgroundImage = `url('${image}')`;
            thumbnail.addEventListener('click', () => {
                mainImage.style.backgroundImage = `url('${image}')`;
                document.querySelectorAll('.modal-thumbnail').forEach(t => t.classList.remove('active'));
                thumbnail.classList.add('active');
            });
            thumbnailsContainer.appendChild(thumbnail);
        });

        // amenities
        const amenitiesContainer = document.getElementById('modalAmenities');
        amenitiesContainer.innerHTML = '';
        room.amenities.forEach(amenity => {
            const amenityElement = document.createElement('div');
            amenityElement.className = 'modal-amenity';
            amenityElement.innerHTML = `<i class="fas fa-check"></i><span>${amenity}</span>`;
            amenitiesContainer.appendChild(amenityElement);
        });
    }

    function closeRoomModal() {
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }
}

// BOOKING SYSTEM // 
function initializeBookingSystem() {
    const bookNowButtons = document.querySelectorAll('.book-now');

    bookNowButtons.forEach(button => {
        button.addEventListener('click', function () {
            const roomType = this.getAttribute('data-room');
            const price = this.getAttribute('data-price');

            // Store booking data in localStorage
            localStorage.setItem('selectedRoom', roomType);
            localStorage.setItem('selectedPrice', price);

            // Redirect to booking page
            window.location.href = 'booking.php';
        });
    });
}

function handleBookingSubmission(form) {
    const formData = new FormData(form);
    const bookingData = {};

    for (let [key, value] of formData.entries()) {
        bookingData[key] = value;
    }
}

// Image Gallery System
function initializeImageGallery() {
    const thumbnails = document.querySelectorAll('.thumbnail');

    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function () {
            const roomCard = this.closest('.room-card-detailed');
            const mainImage = roomCard.querySelector('.main-image');
            const currentThumbnails = roomCard.querySelectorAll('.thumbnail');

            // Update main image
            const newImageUrl = this.style.backgroundImage;
            mainImage.style.backgroundImage = newImageUrl;

            // Update active thumbnail
            currentThumbnails.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });
}

// Scroll Effects
function initializeScrollEffects() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);

    // Observe elements
    document.querySelectorAll('.room-card-detailed, .feature-card, .service-item').forEach(el => {
        observer.observe(el);
    });
}

// CONTACT FORM //
function initializeContactForm() {
    const contactForm = document.getElementById('contactForm');

    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();
            handleContactSubmission(this);
        });
    }
}

function handleContactSubmission(form) {
    const formData = new FormData(form);
    const contactData = {};

    for (let [key, value] of formData.entries()) {
        contactData[key] = value;
    }

}

// Search Functionality
function initializeSearchFunctionality() {
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');

    if (searchInput && searchButton) {
        searchButton.addEventListener('click', performSearch);
        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });
    }
}

function performSearch() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const roomCards = document.querySelectorAll('.room-card-detailed');

    roomCards.forEach(card => {
        const roomTitle = card.querySelector('h3').textContent.toLowerCase();
        const roomDescription = card.querySelector('.room-description').textContent.toLowerCase();

        if (roomTitle.includes(searchTerm) || roomDescription.includes(searchTerm)) {
            card.style.display = 'block';
            card.style.opacity = '1';
        } else {
            card.style.display = 'none';
        }
    });
}

//CAROUSEL FUNCTIONALITY on home page
let currentIndex = 0;
let autoSlideInterval;

function initializeCarousel() {
    const track = document.getElementById('carouselTrack');
    const slides = document.querySelectorAll('.carousel-slide');
    const indicatorContainer = document.getElementById('indicators');

    console.log('Track:', track);
    console.log('Slides:', slides.length);
    console.log('Indicators container:', indicatorContainer);

    if (!track || !slides.length || !indicatorContainer) {
        console.error('Carousel elements not found');
        return;
    }

    // Clear existing indicators
    indicatorContainer.innerHTML = '';

    // Create indicators
    for (let i = 0; i < slides.length; i++) {
        const dot = document.createElement('div');
        dot.classList.add('indicator');
        if (i === 0) dot.classList.add('active');
        dot.addEventListener('click', () => showSlide(i));
        indicatorContainer.appendChild(dot);
    }

    // Initialize
    showSlide(0);
    startAutoPlay();

    // Global functions
    window.nextSlide = nextSlide;
    window.prevSlide = prevSlide;

    console.log('Carousel initialized successfully');
}

function showSlide(index) {
    console.log('Showing slide:', index);
    const track = document.getElementById('carouselTrack');
    const indicators = document.querySelectorAll('.indicator');
    
    if (!track) return;

    // Update track position
    track.style.transform = `translateX(-${index * 25}%)`;
    currentIndex = index;

    // Update indicators
    indicators.forEach((ind, i) => {
        ind.classList.toggle('active', i === index);
    });
}

function nextSlide() {
    const slides = document.querySelectorAll('.carousel-slide');
    const nextIndex = (currentIndex + 1) % slides.length;
    showSlide(nextIndex);
}

function prevSlide() {
    const slides = document.querySelectorAll('.carousel-slide');
    const prevIndex = (currentIndex - 1 + slides.length) % slides.length;
    showSlide(prevIndex);
}

function startAutoPlay() {
    if (autoSlideInterval) clearInterval(autoSlideInterval);
    autoSlideInterval = setInterval(() => {
        console.log('Auto sliding to next');
        nextSlide();
    }, 3000);
}
function tryInitialize() {
    if (document.getElementById('carouselTrack')) {
        initializeCarousel();
    } else {
        setTimeout(tryInitialize, 100);
    }
}

// Start initialization
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', tryInitialize);
} else {
    tryInitialize();
}