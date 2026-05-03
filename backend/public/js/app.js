$(document).ready(function () {

    /**
     * =============================================================================
     * TOAST NOTIFICATION PLUGIN
     * A customizable toast notification system with animations and themes
     * =============================================================================
     */
    document.documentElement.classList.remove('dark');
    document.documentElement.style.colorScheme = 'light';
    function enforceLightMode() {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            // Force light mode
            document.documentElement.style.backgroundColor = "#ffffff"; // White background
            document.documentElement.style.color = "#000000"; // Black text
            document.documentElement.setAttribute("data-theme", "light"); // Optional for CSS usage
        }
    }

    // Run on page load
    enforceLightMode();

    // Listen for changes (if user switches theme)
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', enforceLightMode);

    $.toastr = (function () {
        // -------------------------------------------------------------------------
        // DEFAULT CONFIGURATION
        // -------------------------------------------------------------------------
        const defaults = {
            position: 'top-right',
            duration: 5000,
            closeButton: true,
            progressBar: true,
            escapeHtml: true,
            showDuration: 500,    // Duration for show animation in ms
            hideDuration: 500,    // Duration for hide animation in ms
            animation: 'fade', // Animation type: 'fade', 'slide', 'bounce'
            easing: 'ease'  // CSS easing function for animations
        };

        // Container registry for different positions
        const containers = {};

        // -------------------------------------------------------------------------
        // CONTAINER MANAGEMENT
        // -------------------------------------------------------------------------

        /**
         * Creates a container for toasts at a specific position
         * @param {string} position Position identifier (e.g., 'top-right')
         * @return {jQuery} Container element
         */
        function createContainer(position) {
            const container = $('<div>').addClass(getPositionClass(position));
            $('body').append(container);
            return container;
        }

        /**
         * Get position-specific CSS classes
         * @param {string} position Position identifier
         * @return {string} CSS classes for the specified position
         */
        function getPositionClass(position) {
            const positionClasses = {
                'top-right': 'fixed top-4 right-4 z-50 flex flex-col gap-4 max-w-xs sm:max-w-sm',
                'top-left': 'fixed top-4 left-4 z-50 flex flex-col gap-4 max-w-xs sm:max-w-sm',
                'bottom-right': 'fixed bottom-4 right-4 z-50 flex flex-col gap-4 max-w-xs sm:max-w-sm',
                'bottom-left': 'fixed bottom-4 left-4 z-50 flex flex-col gap-4 max-w-xs sm:max-w-sm',
                'top-center': 'fixed top-4 left-1/2 transform -translate-x-1/2 z-50 flex flex-col gap-4 max-w-xs sm:max-w-sm',
                'bottom-center': 'fixed bottom-4 left-1/2 transform -translate-x-1/2 z-50 flex flex-col gap-4 max-w-xs sm:max-w-sm'
            };
            return positionClasses[position] || positionClasses['top-right'];
        }

        // -------------------------------------------------------------------------
        // TOAST APPEARANCE HELPERS
        // -------------------------------------------------------------------------

        /**
         * Get CSS classes for different toast types
         * @param {string} type Toast type ('success', 'error', 'warning', 'info')
         * @return {string} CSS classes for the specified type
         */
        function getTypeClass(type) {
            const typeClasses = {
                'success': 'bg-green-100 border-l-4 border-green-500 text-green-800',
                'error': 'bg-red-100 border-l-4 border-red-500 text-red-800',
                'warning': 'bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800',
                'info': 'bg-blue-100 border-l-4 border-blue-500 text-blue-800'
            };
            return typeClasses[type] || typeClasses['info'];
        }

        /**
         * Get SVG icon for toast type
         * @param {string} type Toast type
         * @return {string} SVG markup for the icon
         */
        function getIconSVG(type) {
            const icons = {
                'success': `<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>`,
                'error': `<svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>`,
                'warning': `<svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>`,
                'info': `<svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>`
            };
            return icons[type] || icons['info'];
        }

        // -------------------------------------------------------------------------
        // ANIMATION FUNCTIONS
        // -------------------------------------------------------------------------

        /**
         * Apply show animation to toast element
         * @param {jQuery} toast Toast element
         * @param {Object} options Animation options
         */
        function applyShowAnimation(toast, options) {
            const position = options.position;
            const isTop = position.includes('top');
            const transition = `all ${options.showDuration}ms ${options.easing}`;
            switch (options.animation) {
                case 'slide':
                    // Determine slide-in direction based on position
                    if (isTop) {
                        toast.css({
                            'transform': 'translateY(-100%)',
                            'opacity': '0',
                            'transition': transition
                        });
                        setTimeout(() => {
                            toast.css({
                                'transform': 'translateY(0)',
                                'opacity': '1'
                            });
                        }, 10);
                    } else {
                        toast.css({
                            'transform': 'translateY(100%)',
                            'opacity': '0',
                            'transition': transition
                        });
                        setTimeout(() => {
                            toast.css({
                                'transform': 'translateY(0)',
                                'opacity': '1'
                            });
                        }, 10);
                    }
                    break;
                case 'bounce':
                    // Determine bounce-in direction based on position
                    if (isTop) {
                        toast.css({
                            'transform': 'translateY(-120%)',
                            'opacity': '0',
                            'transition': transition
                        });
                        setTimeout(() => {
                            toast.css({
                                'transform': 'translateY(0)',
                                'opacity': '1'
                            });
                            // Add slight bounce effect
                            setTimeout(() => {
                                toast.css({
                                    'transform': 'translateY(-10%)',
                                    'transition': `all 200ms ${options.easing}`
                                });
                                setTimeout(() => {
                                    toast.css({
                                        'transform': 'translateY(0)',
                                        'transition': `all 200ms ${options.easing}`
                                    });
                                }, 200);
                            }, options.showDuration);
                        }, 10);
                    } else {
                        toast.css({
                            'transform': 'translateY(120%)',
                            'opacity': '0',
                            'transition': transition
                        });
                        setTimeout(() => {
                            toast.css({
                                'transform': 'translateY(0)',
                                'opacity': '1'
                            });
                            // Add slight bounce effect
                            setTimeout(() => {
                                toast.css({
                                    'transform': 'translateY(10%)',
                                    'transition': `all 200ms ${options.easing}`
                                });
                                setTimeout(() => {
                                    toast.css({
                                        'transform': 'translateY(0)',
                                        'transition': `all 200ms ${options.easing}`
                                    });
                                }, 200);
                            }, options.showDuration);
                        }, 10);
                    }
                    break;
                case 'fade':
                default:
                    // Simple fade-in
                    toast.css({
                        'opacity': '0',
                        'transition': transition
                    });
                    setTimeout(() => {
                        toast.css('opacity', '1');
                    }, 10);
                    break;
            }
        }

        /**
         * Apply hide animation to toast element
         * @param {jQuery} toast Toast element
         * @param {Object} options Animation options
         */
        function applyHideAnimation(toast, options) {
            const position = options.position;
            const isTop = position.includes('top');
            const transition = `all ${options.hideDuration}ms ${options.easing}`;
            switch (options.animation) {
                case 'slide':
                    if (isTop) {
                        toast.css({
                            'transform': 'translateY(-100%)',
                            'opacity': '0',
                            'transition': transition
                        });
                    } else {
                        toast.css({
                            'transform': 'translateY(100%)',
                            'opacity': '0',
                            'transition': transition
                        });
                    }
                    break;
                case 'bounce':
                    toast.css({
                        'transform': 'scale(0.8)',
                        'opacity': '0',
                        'transition': transition
                    });
                    break;
                case 'fade':
                default:
                    toast.css({
                        'opacity': '0',
                        'transition': transition
                    });
                    break;
            }
        }

        // -------------------------------------------------------------------------
        // TOAST CREATION AND MANAGEMENT
        // -------------------------------------------------------------------------

        /**
         * Create a toast element
         * @param {string} type Toast type
         * @param {string} message Toast message
         * @param {string} title Optional toast title
         * @param {Object} options Toast configuration options
         * @return {jQuery} Created toast element
         */
        function createToast(type, message, title, options) {
            // Create base toast element
            const toastElement = $('<div>').addClass(`w-full rounded-lg shadow-lg overflow-hidden ${getTypeClass(type)}`);
            // Add pulse animation to icon for added emphasis
            const iconAnimation = `
                @keyframes pulse-${type} {
                    0% { transform: scale(1); }
                    50% { transform: scale(1.1); }
                    100% { transform: scale(1); }
                }
            `;

            // Toast inner content with improved layout
            let contentHtml = `
                <style>${iconAnimation}</style>
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-0.5">
                            <div style="animation: pulse-${type} 2s infinite">${getIconSVG(type)}</div>
                        </div>
                        <div class="ml-3 flex-1 mr-2">
            `;
            // Add title if provided with better styling
            if (title) {
                contentHtml += `<p class="text-sm font-semibold break-words">
                    ${options.escapeHtml ? $('<div>').text(title).html() : title}
                </p>`;
            }
            // Add message with better spacing and text wrapping
            contentHtml += `
                <p class="${title ? 'mt-1' : ''} text-sm break-words">
                    ${options.escapeHtml ? $('<div>').text(message).html() : message}
                </p>
            `;
            contentHtml += `</div>`;
            // Add close button if enabled with better positioning
            if (options.closeButton) {
                contentHtml += `
                    <div class="flex-shrink-0 flex">
                        <button class="close-toast inline-flex text-gray-400 hover:text-gray-500 focus:outline-none transition-all duration-300 transform hover:rotate-90">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                `;
            }
            contentHtml += `</div></div>`;
            // Add animated progress bar if enabled
            if (options.progressBar) {
                contentHtml += `
                    <div class="w-full bg-gray-100 h-1 progress-bar-container">
                        <div class="h-1 progress-bar bg-${type === 'info' ? 'blue' : type === 'success' ? 'green' : type === 'warning' ? 'yellow' : 'red'}-500" style="width: 100%; transition: width linear;"></div>
                    </div>
                `;
            }
            toastElement.html(contentHtml);
            return toastElement;
        }

        /**
         * Add toast to container with animations
         * @param {jQuery} toast Toast element
         * @param {string} position Position identifier
         * @param {Object} options Toast configuration options
         */
        function addToast(toast, position, options) {
            // Get or create container for the position
            if (!containers[position]) {
                containers[position] = createContainer(position);
            }
            const container = containers[position];
            // Add toast to container based on position
            const isTopPosition = position.includes('top');
            if (isTopPosition) {
                container.prepend(toast);
            } else {
                container.append(toast);
            }
            // Apply show animation
            applyShowAnimation(toast, options);
        }

        /**
         * Remove toast with animations
         * @param {jQuery} toast Toast element
         * @param {Object} options Toast configuration options
         */
        function removeToast(toast, options = {}) {
            // Default to standard hide duration if not specified
            const hideDuration = options.hideDuration || defaults.hideDuration;
            // Skip if toast is already being removed
            if (toast.data('removing')) return;
            // Mark as removing to prevent duplicate removal
            toast.data('removing', true);
            // Stop any progress update
            const progressInterval = toast.data('progressInterval');
            if (progressInterval) {
                clearInterval(progressInterval);
            }
            // Apply hide animation
            applyHideAnimation(toast, options);
            // Remove element after animation completes
            setTimeout(() => {
                toast.remove();
                // Clean up empty containers
                $.each(containers, (position, container) => {
                    if (container.children().length === 0) {
                        container.remove();
                        delete containers[position];
                    }
                });
            }, hideDuration);
        }

        /**
         * Update progress bar width with animation
         * @param {jQuery} toast Toast element
         * @param {number} progress Progress percentage (0-100)
         */
        function updateProgressBar(toast, progress) {
            const progressBar = toast.find('.progress-bar');
            if (progressBar.length) {
                progressBar.css({
                    'width': progress + '%',
                    'transition': 'width 100ms linear'
                });
            }
        }

        /**
         * Pause toast dismissal
         * @param {jQuery} toast Toast element
         */
        function pauseToast(toast) {
            // If toast is being removed or already paused, do nothing
            if (toast.data('removing') || toast.data('paused')) return;
            // Mark toast as paused
            toast.data('paused', true);
            // Clear the auto dismiss timeout
            const timeout = toast.data('autoDismissTimeout');
            if (timeout) {
                clearTimeout(timeout);
                // Store the remaining time
                const elapsed = Date.now() - toast.data('startTime');
                const remaining = toast.data('duration') - elapsed;
                toast.data('remainingTime', remaining > 0 ? remaining : 0);
            }
            // Pause progress interval
            const progressInterval = toast.data('progressInterval');
            if (progressInterval) {
                clearInterval(progressInterval);
                // Store current progress
                toast.data('currentProgress', toast.find('.progress-bar').width() / toast.find('.progress-bar-container').width() * 100);
            }
            // Add a subtle scale effect to indicate toast is paused
            toast.css({
                'transform': 'scale(1.02)',
                'transition': 'transform 300ms ease',
                'box-shadow': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)'
            });
        }

        /**
         * Resume toast dismissal
         * @param {jQuery} toast Toast element
         * @param {Object} options Toast configuration options
         */
        function resumeToast(toast, options) {
            // If toast is being removed or not paused, do nothing
            if (toast.data('removing') || !toast.data('paused')) return;

            // Reset the subtle scale effect
            toast.css({
                'transform': 'scale(1)',
                'transition': 'transform 300ms ease',
                'box-shadow': ''
            });

            // Get remaining time or use default duration
            const remaining = toast.data('remainingTime') || options.duration;

            // Resume auto dismiss if there's remaining time
            if (remaining > 0) {
                toast.data('autoDismissTimeout', setTimeout(() => {
                    if (toast.parent().length && !toast.data('removing') && !toast.data('paused')) {
                        removeToast(toast, options);
                    }
                }, remaining));

                // Update start time
                toast.data('startTime', Date.now());
                toast.data('duration', remaining);
            }

            // Resume progress bar if enabled
            if (options.progressBar) {
                const currentProgress = toast.data('currentProgress') || 100;
                const progressSteps = remaining / 100; // Update every ~100ms
                let progressValue = currentProgress;

                const progressInterval = setInterval(() => {
                    progressValue = Math.max(0, progressValue - (100 / (remaining / 100)));

                    if (toast.parent().length && !toast.data('removing')) {
                        updateProgressBar(toast, progressValue);
                    }

                    if (progressValue <= 0) {
                        clearInterval(progressInterval);
                    }
                }, 100);

                toast.data('progressInterval', progressInterval);
            }

            // Mark toast as not paused
            toast.data('paused', false);
        }

        /**
         * Add clickable ripple effect to toast
         * @param {jQuery} toast Toast element
         */
        function addRippleEffect(toast) {
            toast.on('mousedown', function (e) {
                if ($(e.target).hasClass('close-toast') || $(e.target).parents('.close-toast').length) {
                    return; // Skip ripple on close button
                }

                const ripple = $('<span>');
                const size = Math.max(toast.width(), toast.height());

                ripple.css({
                    position: 'absolute',
                    top: e.pageY - toast.offset().top - size / 2,
                    left: e.pageX - toast.offset().left - size / 2,
                    width: size + 'px',
                    height: size + 'px',
                    borderRadius: '50%',
                    backgroundColor: 'rgba(255, 255, 255, 0.3)',
                    transform: 'scale(0)',
                    transition: 'transform 600ms linear, opacity 600ms linear',
                    opacity: 1,
                    pointerEvents: 'none'
                });

                toast.append(ripple);

                setTimeout(() => {
                    ripple.css({
                        'transform': 'scale(2)',
                        'opacity': '0'
                    });

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                }, 10);
            });

            // Ensure proper relative positioning for the ripple
            toast.css('position', 'relative');
            toast.css('overflow', 'hidden');
        }

        /**
         * Main toast creation function
         * @param {string} type Toast type
         * @param {string} message Toast message
         * @param {string} title Optional toast title
         * @param {Object} userOptions User configuration options
         * @return {jQuery} Created toast element
         */
        function show(type, message, title, userOptions = {}) {
            // Merge user options with defaults
            const options = $.extend({}, defaults, userOptions);

            // Create toast element
            const toast = createToast(type, message, title, options);

            // Add click event to close button
            toast.find('.close-toast').on('click', function () {
                removeToast(toast, options);
            });

            // Add hover events to pause/resume toast
            toast.on('mouseenter', function () {
                pauseToast(toast);
            }).on('mouseleave', function () {
                resumeToast(toast, options);
            });

            // Add ripple effect on click
            addRippleEffect(toast);

            // Add toast to container with animations
            addToast(toast, options.position, options);

            // Store the start time
            toast.data('startTime', Date.now());
            toast.data('duration', options.duration);

            // Handle progress bar and auto dismiss
            if (options.duration > 0) {
                // For progressBar, update at intervals
                if (options.progressBar) {
                    const intervalDuration = 100; // Update progress every 100ms
                    const steps = options.duration / intervalDuration;
                    let currentStep = 0;

                    const progressInterval = setInterval(() => {
                        // Skip updates if toast is paused
                        if (toast.data('paused')) return;

                        currentStep++;
                        const progress = 100 - ((currentStep / steps) * 100);

                        if (toast.parent().length && !toast.data('removing')) {
                            updateProgressBar(toast, progress);
                        }

                        if (currentStep >= steps) {
                            clearInterval(progressInterval);
                        }
                    }, intervalDuration);

                    toast.data('progressInterval', progressInterval);
                }

                // Auto dismiss after duration
                toast.data('autoDismissTimeout', setTimeout(() => {
                    if (toast.parent().length && !toast.data('removing') && !toast.data('paused')) {
                        removeToast(toast, options);
                    }
                }, options.duration));
            }

            // Return toast element for chaining
            return toast;
        }

        // -------------------------------------------------------------------------
        // PUBLIC API
        // -------------------------------------------------------------------------
        return {
            /**
             * Show success toast notification
             * @param {string} message Toast message
             * @param {string} title Optional toast title
             * @param {Object} options Toast configuration options
             * @return {jQuery} Created toast element
             */
            success: (message, title, options) => show('success', message, title, options),

            /**
             * Show error toast notification
             * @param {string} message Toast message
             * @param {string} title Optional toast title
             * @param {Object} options Toast configuration options
             * @return {jQuery} Created toast element
             */
            error: (message, title, options) => show('error', message, title, options),

            /**
             * Show warning toast notification
             * @param {string} message Toast message
             * @param {string} title Optional toast title
             * @param {Object} options Toast configuration options
             * @return {jQuery} Created toast element
             */
            warning: (message, title, options) => show('warning', message, title, options),

            /**
             * Show info toast notification
             * @param {string} message Toast message
             * @param {string} title Optional toast title
             * @param {Object} options Toast configuration options
             * @return {jQuery} Created toast element
             */
            info: (message, title, options) => show('info', message, title, options),

            /**
             * Set default options for all toasts
             * @param {Object} options Default configuration options
             */
            setDefaults: (options) => {
                $.extend(defaults, options);
            },

            /**
             * Clear all active toasts
             */
            clear: () => {
                $.each(containers, (position, container) => {
                    container.find('div').each(function () {
                        const toast = $(this);

                        // Clear any pending auto-dismiss timeout
                        const timeout = toast.data('autoDismissTimeout');
                        if (timeout) {
                            clearTimeout(timeout);
                        }

                        // Clear any progress interval
                        const interval = toast.data('progressInterval');
                        if (interval) {
                            clearInterval(interval);
                        }

                        removeToast(toast, defaults);
                    });
                });
            }
        };
    })();

    // Add as jQuery plugin
    $.fn.toastr = function (options) {
        return this;
    };

    // ==========================================
    // UTILITY FUNCTIONS
    // ==========================================

    /**
     * Helper function to update or add query string parameter
     */
    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        } else {
            return uri + separator + key + "=" + value;
        }
    }

    
    // User dropdown toggle
    $("#user-menu-button").click(function () {
        if ($("#user-dropdown").hasClass("hidden")) {
            $("#user-dropdown").removeClass("hidden");
            setTimeout(function () {
                $("#user-dropdown").removeClass("opacity-0 scale-95").addClass("opacity-100 scale-100");
            }, 10);
        } else {
            $("#user-dropdown").removeClass("opacity-100 scale-100").addClass("opacity-0 scale-95");
            setTimeout(function () {
                $("#user-dropdown").addClass("hidden");
            }, 200);
        }
    });

    // Close user dropdown when clicking outside
    $(document).click(function (e) {
        if (!$(e.target).closest('#user-menu-button, #user-dropdown').length && !$("#user-dropdown").hasClass("hidden")) {
            $("#user-dropdown").removeClass("opacity-100 scale-100").addClass("opacity-0 scale-95");
            setTimeout(function () {
                $("#user-dropdown").addClass("hidden");
            }, 200);
        }
    });


    $('#notification-button').click(function () {
        if ($('#notification-dropdown').hasClass('hidden')) {
            $('#notification-dropdown').removeClass('hidden');
            setTimeout(function () {
                $('#notification-dropdown').removeClass('opacity-0 scale-95').addClass('opacity-100 scale-100');
            }, 10);
        } else {
            $('#notification-dropdown').removeClass('opacity-100 scale-100').addClass('opacity-0 scale-95');
            setTimeout(function () {
                $('#notification-dropdown').addClass('hidden');
            }, 200);
        }
    });

    // Close Notification Dropdown 
    $(document).click(function (e) {
        if (!$(e.target).closest('#notification-button, #notification-dropdown').length && !$('#notification-dropdown').hasClass('hidden')) {
            $('#notification-dropdown').removeClass('opacity-100 scale-100').addClass('opacity-0 scale-95');
            setTimeout(function () {
                $('#notification-dropdown').addClass('hidden');
            }, 200);
        }
    });


});