@extends('layouts.app')

@section('content')
<div x-data="contactMessageManager()" class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Contact Messages</h1>
            <p class="text-sm text-gray-500 mt-1">Manage user contact inquiries, feedback, and replies</p>
        </div>
    </div>

    <!-- Messages List Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-inbox text-gray-400"></i>
                Received Messages
            </h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-[10px] tracking-widest border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Sender</th>
                        <th class="px-6 py-4">Contact Info</th>
                        <th class="px-6 py-4">Subject</th>
                        <th class="px-6 py-4">Received Date</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="msg in messages" :key="msg.id">
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-4 font-semibold text-gray-600" x-text="msg.id"></td>
                            <td class="px-6 py-4 font-medium text-gray-900" x-text="msg.full_name"></td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-gray-900" x-text="msg.email"></span>
                                    <span class="text-xs text-gray-500" x-text="msg.phone"></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 max-w-xs truncate font-medium text-gray-700" x-text="msg.subject"></td>
                            <td class="px-6 py-4 text-gray-500" x-text="formatDate(msg.created_at)"></td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                      :class="{
                                          'bg-yellow-100 text-yellow-800': msg.status === 'Pending',
                                          'bg-blue-100 text-blue-800': msg.status === 'In Progress',
                                          'bg-emerald-100 text-emerald-800': msg.status === 'Resolved'
                                      }"
                                      x-text="msg.status"></span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openViewModal(msg)" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View & Reply">
                                        <i class="fas fa-reply text-lg"></i>
                                    </button>
                                    <button @click="deleteMessage(msg.id)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                        <i class="fas fa-trash text-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            
            <div x-show="messages.length === 0" class="p-12 text-center text-gray-500">
                <i class="fas fa-envelope-open text-4xl text-gray-300 mb-3"></i>
                <p class="font-medium text-gray-600">No contact messages found</p>
                <p class="text-sm mt-1">Inquiries from the front-end will show up here.</p>
            </div>
        </div>

        <!-- Pagination Controls -->
        <div x-show="pagination.last_page > 1" class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
            <span class="text-sm text-gray-600">
                Showing page <span x-text="pagination.current_page" class="font-semibold text-gray-900"></span> of <span x-text="pagination.last_page" class="font-semibold text-gray-900"></span>
            </span>
            <div class="flex items-center gap-2">
                <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="px-3 py-1 text-sm bg-white border border-gray-200 rounded-md shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">Previous</button>
                <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="px-3 py-1 text-sm bg-white border border-gray-200 rounded-md shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">Next</button>
            </div>
        </div>
    </div>

    <!-- View & Reply Modal -->
    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="isModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="isModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-gray-100">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">Message Details & Reply</h3>
                        <button @click="closeModal()" class="text-gray-400 hover:text-gray-500 bg-gray-50 hover:bg-gray-100 rounded-lg p-2 transition-colors">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <!-- Message Meta Data Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Sender</p>
                                <p class="text-sm font-semibold text-gray-800" x-text="form.full_name"></p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Received Date</p>
                                <p class="text-sm font-semibold text-gray-800" x-text="formatDate(form.created_at)"></p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Email Address</p>
                                <a :href="'mailto:' + form.email" class="text-sm font-semibold text-blue-600 hover:underline" x-text="form.email"></a>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Phone Number</p>
                                <a :href="'tel:' + form.phone" class="text-sm font-semibold text-blue-600 hover:underline" x-text="form.phone"></a>
                            </div>
                        </div>

                        <!-- Message Content -->
                        <div class="space-y-1">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Subject</p>
                            <p class="text-sm font-semibold text-gray-900 bg-gray-50 p-3 rounded-lg border border-gray-100" x-text="form.subject"></p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Message Content</p>
                            <div class="text-sm text-gray-800 bg-gray-50 p-4 rounded-lg border border-gray-100 whitespace-pre-wrap min-h-[100px]" x-text="form.message"></div>
                        </div>

                        <hr class="border-gray-100">

                        <!-- Reply Form -->
                        <form @submit.prevent="saveReply" id="replyForm" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Status</label>
                                    <select x-model="form.status" required class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow">
                                        <option value="Pending">Pending</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Resolved">Resolved</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Admin Response Note</label>
                                <textarea x-model="form.admin_reply" rows="4" placeholder="Type your reply note here..." class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow"></textarea>
                                <p class="text-[11px] text-gray-400">Keep a record of your reply context or actions taken regarding this inquiry.</p>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                    <button type="button" @click="saveReply" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-5 py-2.5 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Save Changes
                    </button>
                    <button type="button" @click="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Cancel
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
        Alpine.data('contactMessageManager', () => ({
            isModalOpen: false,
            
            form: {
                id: null,
                full_name: '',
                email: '',
                phone: '',
                subject: '',
                message: '',
                status: 'Pending',
                admin_reply: '',
                created_at: ''
            },
            
            messages: [],
            pagination: {
                current_page: 1,
                last_page: 1,
                total: 0
            },

            init() {
                this.loadMessages(1);
            },

            loadMessages(page = 1) {
                fetch(`{{ route("contactMessages.data") }}?page=${page}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.data) {
                        this.messages = data.data;
                        this.pagination = {
                            current_page: data.current_page,
                            last_page: data.last_page,
                            total: data.total
                        };
                    } else {
                        this.messages = [];
                        this.pagination = {
                            current_page: 1,
                            last_page: 1,
                            total: 0
                        };
                    }
                })
                .catch(err => {
                    toastr.error('Failed to load contact messages');
                });
            },

            changePage(page) {
                if (page >= 1 && page <= this.pagination.last_page) {
                    this.loadMessages(page);
                }
            },

            openViewModal(msg) {
                fetch(`/panel/contact-messages/${msg.id}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    this.form = {
                        id: data.id,
                        full_name: data.full_name,
                        email: data.email,
                        phone: data.phone,
                        subject: data.subject,
                        message: data.message,
                        status: data.status,
                        admin_reply: data.admin_reply || '',
                        created_at: data.created_at
                    };
                    this.isModalOpen = true;

                    // Automatically update status to 'In Progress' if it was 'Pending'
                    if (data.status === 'Pending') {
                        this.updateStatusToRead(data.id);
                    }
                })
                .catch(err => {
                    toastr.error('Failed to load message details');
                });
            },

            updateStatusToRead(id) {
                let formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('status', 'In Progress');
                formData.append('admin_reply', this.form.admin_reply);

                fetch(`/panel/contact-messages/${id}/update`, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        this.loadMessages(this.pagination.current_page);
                        this.form.status = 'In Progress';
                    }
                });
            },

            closeModal() {
                this.isModalOpen = false;
            },

            deleteMessage(id) {
                if (confirm('Are you sure you want to delete this message inquiry?')) {
                    let formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('_method', 'DELETE');

                    fetch(`/panel/contact-messages/${id}`, {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            toastr.success(res.message);
                            this.loadMessages(this.pagination.current_page);
                        } else {
                            toastr.error('Failed to delete message');
                        }
                    })
                    .catch(err => {
                        toastr.error('Network error');
                    });
                }
            },

            saveReply() {
                let formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('status', this.form.status);
                formData.append('admin_reply', this.form.admin_reply);

                fetch(`/panel/contact-messages/${this.form.id}/update`, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        toastr.success(res.message);
                        this.loadMessages(this.pagination.current_page);
                        this.closeModal();
                    } else {
                        if (res.errors) {
                            Object.values(res.errors).forEach(err => toastr.error(err[0]));
                        } else {
                            toastr.error(res.message || 'Failed to update reply');
                        }
                    }
                })
                .catch(err => {
                    toastr.error('Network error');
                });
            },

            formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
        }));
    });
</script>
@endsection
