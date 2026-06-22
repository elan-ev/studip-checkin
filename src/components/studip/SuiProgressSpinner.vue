<template>
    <div class="sui-progress-spinner" :style="spinnerStyle" role="progressbar" aria-busy="true">
        <svg 
            viewBox="0 0 64 64" 
            xmlns="http://www.w3.org/2000/svg" 
            class="sui-progress-spinner__svg"
        >
            <path 
                fill="currentColor" 
                d="M32,4C16.5,4,4,16.5,4,32h8.3c0-10.9,8.8-19.7,19.7-19.6c10.9,0,19.7,8.8,19.6,19.7c0,10.9-8.8,19.6-19.7,19.6V60c15.5,0,28-12.5,28-28S47.5,4,32,4z"
            >
                <animateTransform 
                    class="sui-progress-spinner__animation"
                    attributeName="transform" 
                    type="rotate" 
                    from="0 32 32" 
                    to="360 32 32" 
                    :dur="`${duration}s`" 
                    repeatCount="indefinite" 
                />
            </path>

            <path 
                fill="currentColor" 
                d="M42.5,32c0-5.8-4.7-10.5-10.5-10.5c-5.8,0-10.5,4.7-10.5,10.5c0,5.8,4.7,10.5,10.5,10.5C37.8,42.6,42.5,37.8,42.5,32C42.5,32,42.5,32,42.5,32z"
            />

            <path 
                v-if="isLargeSize && !disableInnerCircle"
                fill="currentColor" 
                opacity="0.3"
                d="M42.5,32L42.5,32c0,5.8-4.7,10.5-10.5,10.5c-5.8,0-10.5-4.7-10.5-10.5c0-5.8,4.7-10.5,10.5-10.5c0,0,0,0,0,0v-9.2h0c-10.9,0-19.7,8.8-19.7,19.7S21.1,51.7,32,51.7c10.9,0,19.7-8.8,19.7-19.7H42.5z"
            >
                <animateTransform 
                    class="sui-progress-spinner__animation"
                    attributeName="transform" 
                    type="rotate" 
                    from="0 32 32" 
                    to="360 32 32" 
                    :dur="`${duration * 1.5}s`" 
                    repeatCount="indefinite" 
                />
            </path>
        </svg>
    </div>
</template>


<script setup>
import { computed } from 'vue'

const props = defineProps({
    duration: {
        type: Number,
        default: 1.2,
    },
    size: {
        type: [Number, String],
        default: 18,
    },
    color: {
        type: String,
        default: 'currentColor',
    },
    disableInnerCircle: {
        type: Boolean,
        default: true
    }
})

const numericSize = computed(() => {
    if (typeof props.size === 'number') return props.size
    return parseInt(props.size, 10) || 0
})

const isLargeSize = computed(() => numericSize.value >= 32)

const spinnerStyle = computed(() => ({
    width: typeof props.size === 'number' ? `${props.size}px` : props.size,
    height: typeof props.size === 'number' ? `${props.size}px` : props.size,
    color: props.color,
}))
</script>


<style lang="scss" scoped>
.sui-progress-spinner {
    display: inline-flex;
    vertical-align: middle;
    flex-shrink: 0;
    line-height: 0;
    
    &__svg {
        width: 100%;
        height: 100%;
    }
}
</style>