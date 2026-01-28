/**
 * Cloudinary Video Player Initialization
 * Handles initialization and re-initialization of video players for quiz
 */

(function() {
    'use strict';

    // Simple debounce utility
    function debounce(fn, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => fn.apply(this, args), wait);
        };
    }

    // Global player storage
    window.cloudPlayers = [];

    /**
     * Initialize all video players on the page
     */
    window.initPlayers = function() {
        // Clean up existing players
        destroyExistingPlayers();

        // Validate Cloudinary availability
        if (!isCloudinaryAvailable()) {
            console.warn('⚠️ Cloudinary player not available yet');
            return;
        }

        // Initialize players
        initMainPlayer();
        initOptionPlayers();
    };

    /**
     * Destroy all existing players
     */
    function destroyExistingPlayers() {
        try {
            window.cloudPlayers.forEach(player => {
                try {
                    player.destroy();
                } catch(e) {
                    console.warn('Error destroying player:', e);
                }
            });
        } catch(e) {
            console.warn('Error in player cleanup:', e);
        }
        window.cloudPlayers = [];
    }

    /**
     * Check if Cloudinary is available
     */
    function isCloudinaryAvailable() {
        return typeof cloudinary !== 'undefined' && cloudinary.videoPlayer;
    }

    /**
     * Get default player config
     */
    function getPlayerConfig() {
        return {
            cloud_name: 'dmhdsjmzf',
            autoplayMode: 'always',
            controls: false,
            muted: true,
            loop: true,
            fluid: true
        };
    }

    /**
     * Initialize main video player
     */
    function initMainPlayer() {
        const mainElement = document.getElementById('main-video');
        if (!mainElement) return;

        const publicId = mainElement.dataset.publicId || '';
        if (!publicId) return;

        const player = cloudinary.videoPlayer('main-video', getPlayerConfig());
        player.source(publicId);
        window.cloudPlayers.push(player);
    }

    /**
     * Initialize option video players
     */
    function initOptionPlayers() {
        document.querySelectorAll('video.cld-option').forEach(element => {
            const publicId = element.dataset.publicId;
            if (!publicId || !element.id) return;

            const player = cloudinary.videoPlayer(element.id, getPlayerConfig());
            player.source(publicId);
            window.cloudPlayers.push(player);
        });
    }

    // Debounced initialization
    const safeInit = debounce(() => window.initPlayers(), 200);

    /**
     * Set up event listeners
     */
    function setupEventListeners() {
        // DOM ready
        document.addEventListener('DOMContentLoaded', safeInit);

        // Livewire v3
        document.addEventListener('livewire:initialized', () => {
            try {
                Livewire.hook('message.processed', () => safeInit());
            } catch(e) {
                console.warn('Livewire v3 hook error:', e);
            }
        });

        // Livewire v2 (compatibility)
        document.addEventListener('livewire:load', () => {
            try {
                Livewire.hook('message.processed', () => safeInit());
            } catch(e) {
                console.warn('Livewire v2 hook error:', e);
            }
        });

        // Custom events
        // const customEvents = [
        //     'quiz-video-update',
        //     'quiz-video-refresh',
        //     'next-step'
        // ];
        //
        // customEvents.forEach(eventName => {
        //     window.addEventListener(eventName, safeInit);
        // });
    }

    /**
     * Set up DOM mutation observer
     */
    function setupMutationObserver() {
        const root = document.querySelector('#videochoice') || document.body;

        if (!root || !('MutationObserver' in window)) return;

        const observer = new MutationObserver(safeInit);
        observer.observe(root, {
            childList: true,
            subtree: true
        });
    }

    // Initialize everything
    setupEventListeners();
    setupMutationObserver();

})();