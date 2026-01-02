<h2>    
    Daily Sales Report - {{ $date }}  
    <br>
    Total Sales: ${{ number_format($total, 2) }} ({{ $orderCount }} orders)
</h2>

<a href="{{ $downloadUrl }}"
   style="
        display:inline-block;
        padding:10px 18px;
        background:#2563eb;
        color:#ffffff;
        text-decoration:none;
        border-radius:6px;
   ">
    Download Report
</a>