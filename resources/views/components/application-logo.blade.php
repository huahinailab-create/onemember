@props(['dark' => false, 'size' => '1.5rem'])
<span style="font-family:Arial,sans-serif;font-weight:700;font-size:{{ $size }};line-height:1;letter-spacing:-0.01em;" {{ $attributes }}>
    <span style="color:#FF1585;">one</span><span style="color:{{ $dark ? '#ffffff' : '#1A2E5A' }};">member</span>
</span>
