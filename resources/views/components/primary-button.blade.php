<button style="opacity: 0.50" {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary ']) }}>
    {{ $slot }}
</button>
