        // Add to cart function - now integrated with cart system
        function addToCart(productId, productData) {
            // Get the button that was clicked
            const button = event.target.closest('button');
            const originalHTML = button.innerHTML;
            
            // Show loading state
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';
            
            // Simulate a small delay for better UX
            setTimeout(() => {
                if (window.cart && productData) {
                    cart.addToCart(productId, productData);
                } else {
                    // Fallback if cart is not loaded yet
                    alert('Added to cart: ' + (productData ? productData.name : 'Product ID ' + productId));
                }
                
                // Reset button state
                button.disabled = false;
                button.innerHTML = originalHTML;
                
                // Briefly show success state
                button.innerHTML = '<i class="fas fa-check me-2"></i>Added!';
                button.classList.add('btn-success');
                button.classList.remove('btn-primary');
                
                setTimeout(() => {
                    button.innerHTML = originalHTML;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-primary');
                }, 1000);
            }, 300);
        }