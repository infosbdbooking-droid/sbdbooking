@extends('layouts.app')

@section('content')
<div x-data="sliderManager()" class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Slider Management</h1>
            <p class="text-sm text-gray-500 mt-1">Manage homepage hero sliders and promotional banners</p>
        </div>
        <button @click="openAddModal()" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-medium transition-all shadow-sm hover:shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Add New Slider
        </button>
    </div>

    <!-- Sliders List Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                </svg>
                Active Sliders
            </h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-[10px] tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 w-10"></th>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Image</th>
                        <th class="px-6 py-4">Image Title</th>
                        <th class="px-6 py-4">Alt / Meta</th>
                        <th class="px-6 py-4 text-center">Order</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="slider-list">
                    <template x-for="slider in sliders" :key="slider.id">
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-4 text-gray-300 hover:text-gray-500 cursor-move transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                </svg>
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-600" x-text="slider.slider_id"></td>
                            <td class="px-6 py-4">
                                <div class="h-12 w-24 rounded-lg bg-gray-100 overflow-hidden border border-gray-200">
                                    <img :src="slider.image" :alt="slider.alt" class="h-full w-full object-cover">
                                </div>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900" x-text="slider.title"></td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-xs text-gray-600" x-text="'Alt: ' + slider.alt"></span>
                                    <span class="text-xs text-gray-400" x-text="'Meta: ' + slider.meta_title"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-gray-100 text-gray-600 font-bold text-xs border border-gray-200" x-text="slider.order"></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button @click="toggleStatus(slider.id)" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none" :class="slider.status === 'Active' ? 'bg-emerald-500' : 'bg-gray-300'">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform" :class="slider.status === 'Active' ? 'translate-x-6' : 'translate-x-1'"></span>
                                </button>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button @click="openEditModal(slider)" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                        </svg>
                                    </button>
                                    <button @click="deleteSlider(slider.id)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            
            <div x-show="sliders.length === 0" class="p-12 text-center text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="font-medium text-gray-600">No sliders found</p>
                <p class="text-sm mt-1">Click "Add New Slider" to create your first one.</p>
            </div>
        </div>
    </div>

    <!-- Add / Edit Modal -->
    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="isModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="isModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-100">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title" x-text="isEditing ? 'Edit Slider' : 'Add New Slider'"></h3>
                        <button @click="closeModal()" class="text-gray-400 hover:text-gray-500 bg-gray-50 hover:bg-gray-100 rounded-lg p-2 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="saveSlider" id="sliderForm">
                        <div class="space-y-5">
                            
                            <!-- First Row -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Slider ID</label>
                                    <input type="text" x-model="form.slider_id" readonly class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-gray-500 font-mono text-sm cursor-not-allowed focus:outline-none">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Display Order</label>
                                    <input type="number" x-model="form.order" min="1" required class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow">
                                </div>
                            </div>

                            <!-- Image Upload -->
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Slider Image <span class="text-red-500">*</span></label>
                                <div class="relative group mt-1">
                                    <input type="file" @change="handleImageUpload" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" :required="!isEditing && !form.imagePreview">
                                    <div class="border-2 border-dashed border-gray-200 rounded-2xl p-6 flex flex-col items-center justify-center min-h-[180px] group-hover:border-blue-400 group-hover:bg-blue-50/30 transition-all bg-gray-50/50">
                                        
                                        <template x-if="form.imagePreview">
                                            <div class="relative w-full h-full flex flex-col items-center">
                                                <img :src="form.imagePreview" class="max-h-[140px] rounded-lg shadow-sm border border-gray-200">
                                                <p class="text-xs text-blue-600 font-medium mt-3 bg-white px-3 py-1 rounded-full shadow-sm border border-blue-100">Click or drag to change image</p>
                                            </div>
                                        </template>
                                        
                                        <template x-if="!form.imagePreview">
                                            <div class="flex flex-col items-center text-center">
                                                <div class="h-12 w-12 bg-white rounded-full shadow-sm border border-gray-100 flex items-center justify-center mb-3 text-gray-400 group-hover:text-blue-500 group-hover:scale-110 transition-all">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                    </svg>
                                                </div>
                                                <p class="text-sm text-gray-600 font-medium"><span class="text-blue-600 font-semibold">Click to upload</span> or drag and drop</p>
                                                <p class="text-xs text-gray-400 mt-1">PNG, JPG, WEBP up to 2MB (Recommended: 1920x800px)</p>
                                            </div>
                                        </template>

                                    </div>
                                </div>
                            </div>

                            <!-- Text Inputs -->
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Image Title</label>
                                <input type="text" x-model="form.title" placeholder="e.g. Summer Special Offer" required class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Alt Tag (SEO)</label>
                                    <input type="text" x-model="form.alt" placeholder="Descriptive text for image" required class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Meta Title (SEO)</label>
                                    <input type="text" x-model="form.meta_title" placeholder="Meta title for search engines" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow">
                                </div>
                            </div>

                            <!-- Status Toggle -->
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
                                <div>
                                    <p class="text-sm font-bold text-gray-900">Slider Status</p>
                                    <p class="text-xs text-gray-500">Determine if this slider is visible on the website.</p>
                                </div>
                                <button type="button" @click="form.status = (form.status === 'Active' ? 'Inactive' : 'Active')" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none" :class="form.status === 'Active' ? 'bg-emerald-500' : 'bg-gray-300'">
                                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="form.status === 'Active' ? 'translate-x-5' : 'translate-x-0'"></span>
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="bg-gray-50 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                    <button type="button" @click="saveSlider" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-5 py-2.5 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Save Slider
                    </button>
                    <button type="button" @click="resetForm" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('sliderManager', () => ({
            isModalOpen: false,
            isEditing: false,
            
            form: {
                id: null,
                slider_id: '',
                title: '',
                alt: '',
                meta_title: '',
                order: 1,
                status: 'Active',
                imagePreview: null,
                imageFile: null
            },
            
            sliders: [
                {
                    id: 1,
                    slider_id: 'SLD-2026-001',
                    title: 'Summer Car Rental Sale',
                    alt: 'Summer car rental discount banner',
                    meta_title: 'Save 20% on Summer Rentals',
                    order: 1,
                    status: 'Active',
                    image: 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?auto=format&fit=crop&q=80&w=1920&h=800'
                },
                {
                    id: 2,
                    slider_id: 'SLD-2026-002',
                    title: 'Premium Airport Transfers',
                    alt: 'Luxury car at airport',
                    meta_title: 'Reliable Airport Taxi Service',
                    order: 2,
                    status: 'Active',
                    image: 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&q=80&w=1920&h=800'
                },
                {
                    id: 3,
                    slider_id: 'SLD-2026-003',
                    title: 'Outstation Travel Packages',
                    alt: 'Family traveling in SUV',
                    meta_title: 'Book Outstation Cabs Online',
                    order: 3,
                    status: 'Inactive',
                    image: 'https://images.unsplash.com/photo-1517524008697-84bbe3c3fd98?auto=format&fit=crop&q=80&w=1920&h=800'
                }
            ],

            generateId() {
                const random = Math.floor(100 + Math.random() * 900);
                return `SLD-2026-${random}`;
            },

            openAddModal() {
                this.isEditing = false;
                this.resetForm();
                this.form.slider_id = this.generateId();
                this.form.order = this.sliders.length + 1;
                this.isModalOpen = true;
            },

            openEditModal(slider) {
                this.isEditing = true;
                this.form = {
                    id: slider.id,
                    slider_id: slider.slider_id,
                    title: slider.title,
                    alt: slider.alt,
                    meta_title: slider.meta_title,
                    order: slider.order,
                    status: slider.status,
                    imagePreview: slider.image,
                    imageFile: null
                };
                this.isModalOpen = true;
            },

            closeModal() {
                this.isModalOpen = false;
            },

            resetForm() {
                const currentId = this.form.slider_id;
                this.form = {
                    id: null,
                    slider_id: currentId,
                    title: '',
                    alt: '',
                    meta_title: '',
                    order: this.sliders.length + (this.isEditing ? 0 : 1),
                    status: 'Active',
                    imagePreview: null,
                    imageFile: null
                };
                // Reset file input
                const fileInput = document.querySelector('input[type="file"]');
                if(fileInput) fileInput.value = '';
            },

            handleImageUpload(event) {
                const file = event.target.files[0];
                if (!file) return;
                
                this.form.imageFile = file;
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.form.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            },

            toggleStatus(id) {
                // Basic AJAX mockup
                const slider = this.sliders.find(s => s.id === id);
                if (slider) {
                    const newStatus = slider.status === 'Active' ? 'Inactive' : 'Active';
                    slider.status = newStatus;
                    toastr.success(`Status updated to ${newStatus}`);
                }
            },

            deleteSlider(id) {
                if (confirm('Are you sure you want to delete this slider?')) {
                    // Basic AJAX mockup
                    this.sliders = this.sliders.filter(s => s.id !== id);
                    toastr.success('Slider deleted successfully');
                }
            },

            saveSlider() {
                // Validation
                if (!this.form.title || !this.form.alt) {
                    toastr.error('Please fill all required fields');
                    return;
                }
                
                if (!this.isEditing && !this.form.imageFile) {
                    toastr.error('Please upload an image');
                    return;
                }

                // Basic AJAX save mockup
                if (this.isEditing) {
                    const index = this.sliders.findIndex(s => s.id === this.form.id);
                    if (index !== -1) {
                        this.sliders[index] = {
                            ...this.sliders[index],
                            title: this.form.title,
                            alt: this.form.alt,
                            meta_title: this.form.meta_title,
                            order: parseInt(this.form.order),
                            status: this.form.status,
                            image: this.form.imagePreview // In real app, this would be new uploaded URL
                        };
                        toastr.success('Slider updated successfully');
                    }
                } else {
                    const newSlider = {
                        id: Date.now(),
                        slider_id: this.form.slider_id,
                        title: this.form.title,
                        alt: this.form.alt,
                        meta_title: this.form.meta_title,
                        order: parseInt(this.form.order),
                        status: this.form.status,
                        image: this.form.imagePreview
                    };
                    this.sliders.push(newSlider);
                    toastr.success('Slider created successfully');
                }
                
                // Sort by order
                this.sliders.sort((a, b) => a.order - b.order);
                this.closeModal();
            }
        }));
    });
</script>
@endsection
