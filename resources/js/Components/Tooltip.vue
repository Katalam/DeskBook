<script setup lang="ts">
import {Ref, ref} from "vue";

const show : Ref = ref(false);
const timeout : Ref = ref(null);

function showTooltip() {
    timeout.value = setTimeout(() => {
        show.value = true;
    }, 500);
}
function hideTooltip() {
    clearTimeout(timeout.value);
    show.value = false;
}
</script>

<template>
    <div class="relative">
        <div @mouseover="showTooltip" @mouseleave="hideTooltip" class="flex">
            <slot name="trigger">

            </slot>
        </div>
        <div class="absolute top-12 -left-16 text-gray-600 w-max bg-background-light px-4 py-1 rounded-lg" v-if="show">
            <slot name="content">
            </slot>
        </div>
    </div>
</template>
