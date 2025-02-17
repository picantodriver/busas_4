<div class="w-full flex justify-between items-center px-6 py-5 shadow-md rounded-lg" style="background: linear-gradient(90deg, #26a8d3, #00aedd, #00b6e4, #00cbf7);">
    <div class="flex items-center space-x-2 text-white font-medium">
        <!-- Welcome message -->
        <span>Welcome, {{ strtoupper(auth()->user()->name) }}! <span style="font-size: 1rem;">🎉</span></span>
    </div>
    <div class="flex items-center space-x-2 text-white font-medium">
        <x-heroicon-o-calendar class="w-5 h-5 text-white" />
        <!-- Real-time updating datetime span -->
        <span id="currentDateTime">{{ $currentDateTime }}</span>
    </div>
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