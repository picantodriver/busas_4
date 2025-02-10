<div class="w-full flex justify-between items-center px-6 py-5 shadow-md rounded-lg" style="background: linear-gradient(90deg, #26a8d3, #00aedd, #00b6e4, #00cbf7);">
    <div class="flex items-center space-x-2 text-white font-medium">
        <x-heroicon-o-calendar class="w-5 h-5 text-white"/>
        <!-- Real-time updating datetime span -->
        <span id="currentDateTime">{{ $currentDateTime }}</span>
    </div>
    <!-- <div class="flex items-center space-x-3">
        <x-heroicon-o-bell class="w-6 h-6 text-white relative">
            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full px-1">1</span>
        </x-heroicon-o-bell>
        <x-filament::avatar :user="auth()->user()" />
    </div> -->
</div>

<script>
    // Function to update the time
    function updateTime() {
        const date = new Date();
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: '2-digit', 
            day: '2-digit', 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit', 
            hour12: true 
        };
        const formattedDate = date.toLocaleString('en-PH', options);
        
        document.getElementById('currentDateTime').textContent = formattedDate;
    }
    setInterval(updateTime, 1000);
    updateTime();
</script>
