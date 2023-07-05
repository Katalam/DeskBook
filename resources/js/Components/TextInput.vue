<script setup lang="ts">
import {onMounted, Ref, ref} from 'vue';

defineProps<{
    modelValue: string | number;
}>();

defineEmits(['update:modelValue']);

const input : Ref = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        ref="input"
        class="border-gray-300 bg-gray-200 text-gray-800 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
    >
</template>
