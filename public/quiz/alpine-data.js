function quizData() {
    return {
        slow: false,
        openCongrats: false,
        showFailModal: false,
        score: 0,
        openSubscription: false,
        slideOut: false,

        toggleSpeed() {
            this.slow = !this.slow;
            document.querySelectorAll('video').forEach(v => {
                v.playbackRate = this.slow ? 0.5 : 1;
            });
        },

        init() {
            this.$watch('openCongrats', value => {
                if (value) this.showFailModal = false;
            });

            // Event listeners
            window.addEventListener('quiz-failed', (event) => {
                this.openCongrats = false;
                this.showFailModal = true;
                this.score = event.detail.percentage;
            });

            window.addEventListener('quiz-finished', () => {
                this.showFailModal = false;
                this.openCongrats = true;
            });

            window.addEventListener('subscription-required', () => {
                this.openSubscription = true;
            });

            window.addEventListener('next-step', () => {
                this.slideOut = true;
                setTimeout(() => {
                    this.slideOut = false;
                }, 650);
            });
        }
    };
}