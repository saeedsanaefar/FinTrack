function transactionForm() {
    return {
        form: {
            description: '',
            amount: '',
            category_id: '',
            account_id: '',
            type: 'expense',
            date: new Date().toISOString().split('T')[0],
            notes: ''
        },
        suggestions: [],
        calculatedAmount: null,
        calculationError: false,
        isSubmitting: false,

        // Smart category suggestion based on description
        suggestCategory() {
            if (this.form.description.length < 3) {
                this.suggestions = [];
                return;
            }

            // Enhanced keyword matching for suggestions
            const keywords = {
                // Food & Dining
                'grocery': { name: 'Groceries', type: 'expense' },
                'restaurant': { name: 'Dining Out', type: 'expense' },
                'coffee': { name: 'Coffee & Tea', type: 'expense' },
                'lunch': { name: 'Dining Out', type: 'expense' },
                'dinner': { name: 'Dining Out', type: 'expense' },

                // Transportation
                'gas': { name: 'Gas & Fuel', type: 'expense' },
                'uber': { name: 'Rideshare', type: 'expense' },
                'taxi': { name: 'Rideshare', type: 'expense' },
                'parking': { name: 'Parking', type: 'expense' },

                // Income
                'salary': { name: 'Salary', type: 'income' },
                'freelance': { name: 'Freelance', type: 'income' },
                'bonus': { name: 'Bonus', type: 'income' },

                // Housing
                'rent': { name: 'Rent', type: 'expense' },
                'mortgage': { name: 'Mortgage', type: 'expense' },
                'utilities': { name: 'Utilities', type: 'expense' },

                // Shopping
                'amazon': { name: 'Online Shopping', type: 'expense' },
                'shopping': { name: 'Shopping', type: 'expense' },
                'clothes': { name: 'Clothing', type: 'expense' }
            };

            const description = this.form.description.toLowerCase();
            this.suggestions = Object.entries(keywords)
                .filter(([keyword, data]) => {
                    return description.includes(keyword) &&
                           (data.type === this.form.type || data.type === 'both');
                })
                .map(([keyword, data]) => ({
                    id: keyword,
                    name: data.name,
                    type: data.type
                }))
                .slice(0, 5); // Limit to 5 suggestions
        },

        // Select suggested category
        selectCategory(suggestion) {
            // Find the actual category ID from the available categories
            const categorySelect = document.querySelector('select[x-model="form.category_id"]');
            const options = Array.from(categorySelect.options);
            const matchingOption = options.find(option =>
                option.textContent.toLowerCase().includes(suggestion.name.toLowerCase())
            );

            if (matchingOption) {
                this.form.category_id = matchingOption.value;
            }

            this.suggestions = [];
        },

        // Calculator functionality for amount input
        calculateAmount() {
            this.calculationError = false;
            this.calculatedAmount = null;

            if (!this.form.amount) return;

            try {
                // Clean the input and validate it's safe
                const cleanInput = this.form.amount.replace(/[^0-9+\-*/.() ]/g, '');

                // Basic validation to prevent code injection
                if (!/^[0-9+\-*/.() ]+$/.test(cleanInput)) {
                    throw new Error('Invalid characters');
                }

                // Evaluate the expression safely
                const result = Function(`"use strict"; return (${cleanInput})`)();

                if (isNaN(result) || !isFinite(result) || result < 0) {
                    throw new Error('Invalid result');
                }

                this.calculatedAmount = parseFloat(result).toFixed(2);

                // Auto-update the form amount if it's a calculation
                if (cleanInput !== this.form.amount && cleanInput.includes(/[+\-*/]/)) {
                    this.form.amount = this.calculatedAmount;
                }

            } catch (e) {
                this.calculationError = true;
                this.calculatedAmount = null;
            }
        },

        // Submit form
        async submitForm() {
            this.isSubmitting = true;

            try {
                const formData = new FormData();
                Object.keys(this.form).forEach(key => {
                    if (this.form[key]) {
                        formData.append(key, this.form[key]);
                    }
                });

                const response = await fetch('/transactions', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    // Success - redirect or show success message
                    window.location.href = '/transactions?success=created';
                } else {
                    throw new Error('Failed to save transaction');
                }

            } catch (error) {
                alert('Error saving transaction: ' + error.message);
            } finally {
                this.isSubmitting = false;
            }
        },

        // Reset form
        resetForm() {
            this.form = {
                description: '',
                amount: '',
                category_id: '',
                account_id: '',
                type: 'expense',
                date: new Date().toISOString().split('T')[0],
                notes: ''
            };
            this.suggestions = [];
            this.calculatedAmount = null;
            this.calculationError = false;
        }
    }
}

// Global helper functions for mobile interactions
function editTransaction(id) {
    window.location.href = `/transactions/${id}/edit`;
}

function duplicateTransaction(id) {
    if (confirm('Create a copy of this transaction?')) {
        fetch(`/transactions/${id}/duplicate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }).then(response => {
            if (response.ok) {
                window.location.reload();
            }
        });
    }
}
