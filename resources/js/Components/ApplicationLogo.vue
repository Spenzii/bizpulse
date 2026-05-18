<script setup>
defineProps({
    variant: {
        type: String,
        default: 'default' // 'default', 'black', 'white'
    }
});
</script>

<template>
    <img 
        v-if="variant === 'white'"
        src="/images/logo-white.png" 
        alt="BizPulse Logo" 
        class="object-contain max-h-full" 
    />
    <img 
        v-else-if="variant === 'black'"
        src="/images/logo-black.png" 
        alt="BizPulse Logo" 
        class="object-contain max-h-full" 
    />
    <img 
        v-else
        src="/images/logo.png" 
        alt="BizPulse Logo" 
        class="object-contain max-h-full" 
    />
</template>
