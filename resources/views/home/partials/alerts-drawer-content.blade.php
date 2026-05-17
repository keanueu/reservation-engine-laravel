{{-- High-Performance Lightweight Alerts Drawer --}}
<div x-data="alertDrawer()" 
     x-init="init()"
     @keydown.window.escape="open = false"
     x-cloak>
    
    {{-- Trigger Button - Minimal Footprint --}}
    <button @click="open = !open; hasOpened = true; fetchAll()" 
            class="fixed left-0 top-1/4 z-[60] flex items-center gap-2 py-3 px-3 rounded-r-lg shadow transition-transform duration-200"
            :class="open ? '-translate-x-full' : 'translate-x-0 bg-[#111] text-white border-r border-white/5'">
        
        <div class="relative">
            <span class="material-symbols-outlined text-xl">notifications</span>
            <template x-if="unreadCount > 0">
                <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-[#A15D1A] rounded-full border border-black"></span>
            </template>
        </div>
        <span class="text-[9px] font-bold uppercase tracking-widest hidden md:inline">Alerts</span>
    </button>

    {{-- Performance Backdrop --}}
    <div x-show="open" 
         x-transition:enter="transition-opacity duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 bg-black/50 z-[55]"></div>

    {{-- Optimized Sidebar --}}
    <aside class="fixed top-0 left-0 w-72 md:w-80 h-full bg-white shadow-2xl z-[60] flex flex-col transition-transform duration-200 ease-out"
           :class="open ? 'translate-x-0' : '-translate-x-full'"
           style="will-change: transform;">
        
        <div class="p-5 border-b border-gray-50 flex items-center justify-between">
            <h2 class="text-lg font-bold text-black uppercase tracking-tight">Status <span class="text-[#63360D]">Center</span></h2>
            <button @click="open = false" class="w-8 h-8 rounded-full hover:bg-gray-100 transition-colors">
                <span class="material-symbols-outlined text-lg text-gray-400">close</span>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-5">
            {{-- Global Status --}}
            <div class="p-4 rounded-lg border transition-colors"
                 :class="{
                     'bg-green-50 border-green-100 text-green-900': globalStatus === 'Normal',
                     'bg-amber-50 border-amber-100 text-amber-900': globalStatus === 'Advisory',
                     'bg-red-50 border-red-100 text-red-900': globalStatus === 'Immediate Danger',
                 }">
                <div class="flex gap-3">
                    <span class="material-symbols-outlined" x-text="globalStatus === 'Normal' ? 'check_circle' : 'warning'"></span>
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wide" x-text="globalStatus + ' Status'"></h3>
                        <p class="mt-1 text-[11px] leading-relaxed opacity-90" x-text="globalMessage"></p>
                    </div>
                </div>
            </div>

            {{-- Personal Alerts --}}
            @auth
                <div class="space-y-3">
                    <h4 class="text-[9px] font-bold text-gray-400 uppercase tracking-widest px-1">Recent Notifications</h4>
                    <div class="space-y-2">
                        <template x-for="alert in personalAlerts" :key="alert.id">
                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-black truncate" x-text="alert.data.title || 'Alert'"></p>
                                        <p class="text-[10px] text-gray-500 mt-1 line-clamp-2" x-text="alert.data.message"></p>
                                    </div>
                                    <button @click="markAsRead(alert.id)" class="text-[9px] font-bold text-[#63360D] hover:underline uppercase ml-2">Clear</button>
                                </div>
                            </div>
                        </template>
                        <template x-if="personalAlerts.length === 0">
                            <div class="py-8 text-center bg-gray-50/50 rounded-lg border border-dashed border-gray-100">
                                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">No New Alerts</p>
                            </div>
                        </template>
                    </div>
                </div>
            @endauth
        </div>

        <div class="p-4 border-t border-gray-50">
            <button class="w-full py-2.5 bg-[#111] text-white text-[9px] font-bold uppercase tracking-widest rounded-md hover:bg-[#63360D] transition-colors">
                Safety Info
            </button>
        </div>
    </aside>
</div>

<script>
function alertDrawer() {
    return {
        open: false,
        hasOpened: false,
        globalStatus: 'Normal',
        globalMessage: 'System is monitoring all safety channels.',
        personalAlerts: [],
        unreadCount: 0,
        
        init() {
            // Lazy load data to keep initial page speed high
            setTimeout(() => this.fetchAll(), 2000);
            // Longer interval (3 min) to save CPU
            setInterval(() => this.fetchAll(), 180000);
        },
        
        fetchAll() {
            this.fetchGlobal();
            @auth this.fetchPersonal(); @endauth
        },
        
        async fetchGlobal() {
            try {
                const res = await fetch('/admin/alerts');
                if (!res.ok) return;
                const data = await res.json();
                const alerts = Array.isArray(data) ? data : (data.data || []);
                if (alerts.length > 0) {
                    const l = alerts[0];
                    const s = (l.severity || '').toLowerCase();
                    this.globalStatus = (s === 'danger' || s === 'critical') ? 'Immediate Danger' : (s === 'warning' ? 'Advisory' : 'Normal');
                    this.globalMessage = l.message;
                }
            } catch (e) {}
        },
        
        async fetchPersonal() {
            try {
                const res = await fetch('/api/user/notifications');
                if (!res.ok) return;
                const data = await res.json();
                this.personalAlerts = Array.isArray(data.data) ? data.data : [];
                this.unreadCount = this.personalAlerts.length;
            } catch (e) {}
        },
        
        async markAsRead(id) {
            try {
                const res = await fetch(`/api/user/notifications/${id}/read`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                });
                if (res.ok) this.fetchPersonal();
            } catch (e) {}
        }
    }
}
</script>
