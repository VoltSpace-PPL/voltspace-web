<script>
(function() {
    // Intercept value and selectedIndex setters on HTMLSelectElement
    const originalValDesc = Object.getOwnPropertyDescriptor(HTMLSelectElement.prototype, 'value');
    if (originalValDesc) {
        Object.defineProperty(HTMLSelectElement.prototype, 'value', {
            get() { return originalValDesc.get.call(this); },
            set(val) {
                originalValDesc.set.call(this, val);
                this.dispatchEvent(new Event('vs-value-changed'));
            }
        });
    }

    const originalIndexDesc = Object.getOwnPropertyDescriptor(HTMLSelectElement.prototype, 'selectedIndex');
    if (originalIndexDesc) {
        Object.defineProperty(HTMLSelectElement.prototype, 'selectedIndex', {
            get() { return originalIndexDesc.get.call(this); },
            set(val) {
                originalIndexDesc.set.call(this, val);
                this.dispatchEvent(new Event('vs-value-changed'));
            }
        });
    }

    function initCustomSelect(select) {
        if (select.dataset.customSelectInitialized) return;
        select.dataset.customSelectInitialized = true;
        select.style.display = 'none';

        const wrapper = document.createElement('div');
        wrapper.className = 'relative w-full custom-select-wrapper';
        select.parentNode.insertBefore(wrapper, select);
        wrapper.appendChild(select);

        const trigger = document.createElement('div');
        let baseClass = select.className.replace(/appearance-none/g, '').trim();
        // Fallback styling if select didn't have much styling
        if (!baseClass) {
            baseClass = 'w-full bg-[#161e2d] border border-white/10 rounded-xl px-4 py-3 text-[14px] text-white focus:outline-none transition-colors';
        }
        trigger.className = baseClass + ' flex items-center justify-between cursor-pointer min-h-[48px]';
        
        trigger.innerHTML = `<span class="select-text truncate text-[14px]"></span>
                             <svg class="w-4 h-4 text-slate-500 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" stroke-width="2"/></svg>`;
        wrapper.appendChild(trigger);

        const dropdown = document.createElement('div');
        dropdown.className = 'absolute z-[9999] w-full mt-2 bg-[#161e2d] border border-white/10 rounded-xl shadow-2xl overflow-hidden hidden max-h-60 overflow-y-auto custom-scrollbar';
        // Add a slight backdrop blur to dropdown
        dropdown.style.backdropFilter = 'blur(10px)';
        dropdown.style.background = 'rgba(22, 30, 45, 0.95)';
        wrapper.appendChild(dropdown);

        function renderOptions() {
            dropdown.innerHTML = '';
            Array.from(select.options).forEach((option, index) => {
                const item = document.createElement('div');
                const isSelected = select.selectedIndex === index;
                item.className = 'px-4 py-3 text-[14px] transition-colors cursor-pointer ' + 
                                 (isSelected ? 'bg-[#00d4aa]/10 text-[#00d4aa] font-bold' : 'text-slate-300 hover:bg-white/5');
                item.textContent = option.text;
                
                item.addEventListener('click', (e) => {
                    e.stopPropagation();
                    select.selectedIndex = index;
                    // Trigger native events so listeners work
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    updateTrigger();
                    dropdown.classList.add('hidden');
                });
                dropdown.appendChild(item);
            });
            updateTrigger();
        }

        function updateTrigger() {
            const selectedOption = select.options[select.selectedIndex];
            trigger.querySelector('.select-text').textContent = selectedOption ? selectedOption.text : 'Pilih...';
            
            // Re-render selection styles
            Array.from(dropdown.children).forEach((c, i) => {
                if (i === select.selectedIndex) {
                    c.className = 'px-4 py-3 text-[14px] cursor-pointer transition-colors bg-[#00d4aa]/10 text-[#00d4aa] font-bold';
                } else {
                    c.className = 'px-4 py-3 text-[14px] text-slate-300 hover:bg-white/5 cursor-pointer transition-colors';
                }
            });
        }

        renderOptions();

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isHidden = dropdown.classList.contains('hidden');
            
            // Close all others
            document.querySelectorAll('.custom-select-dropdown').forEach(d => d.classList.add('hidden'));
            
            if (isHidden) {
                dropdown.classList.remove('hidden');
                
                // Smart placement logic (open upwards if close to bottom)
                const rect = trigger.getBoundingClientRect();
                const spaceBelow = window.innerHeight - rect.bottom;
                const spaceAbove = rect.top;
                
                if (spaceBelow < 250 && spaceAbove > spaceBelow) {
                    dropdown.style.bottom = '100%';
                    dropdown.style.top = 'auto';
                    dropdown.style.marginTop = '0';
                    dropdown.style.marginBottom = '8px';
                } else {
                    dropdown.style.top = '100%';
                    dropdown.style.bottom = 'auto';
                    dropdown.style.marginTop = '8px';
                    dropdown.style.marginBottom = '0';
                }

                // Scroll to selected
                const selected = dropdown.children[select.selectedIndex];
                if (selected) {
                    dropdown.scrollTop = selected.offsetTop - dropdown.clientHeight / 2;
                }
            }
        });

        dropdown.classList.add('custom-select-dropdown');

        // Sync programmatic changes
        select.addEventListener('vs-value-changed', () => {
            updateTrigger();
        });
        
        // Listen to DOM mutations (e.g. innerHTML changes)
        const observer = new MutationObserver(() => {
            renderOptions();
        });
        observer.observe(select, { childList: true });
    }

    // Initialize globally
    function initAll() {
        document.querySelectorAll('select').forEach(initCustomSelect);
    }

    document.addEventListener('DOMContentLoaded', initAll);
    
    // Fallback for dynamically added selects later
    const bodyObserver = new MutationObserver((mutations) => {
        let shouldInit = false;
        mutations.forEach(m => {
            if (m.addedNodes.length > 0) {
                for (let i=0; i<m.addedNodes.length; i++) {
                    const node = m.addedNodes[i];
                    if (node.nodeType === 1) { // ELEMENT_NODE
                        if (node.tagName === 'SELECT' || node.querySelector('select')) {
                            shouldInit = true;
                        }
                    }
                }
            }
        });
        if (shouldInit) {
            initAll();
        }
    });
    bodyObserver.observe(document.body, { childList: true, subtree: true });

    // Global click listener to close dropdowns
    document.addEventListener('click', () => {
        document.querySelectorAll('.custom-select-dropdown').forEach(d => d.classList.add('hidden'));
    });
})();
</script>
