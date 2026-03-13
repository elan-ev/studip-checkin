<template>
    <header class="checkin-header">
        <h2 class="checkin-header-content">
            <slot name="title"></slot>
        </h2>
    </header>
</template>

<script setup>

</script>

<style lang="scss">
.checkin-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
    position: relative;
    background-color: var(--color--global-background);
    border-bottom: 1px solid #ddd;

    .checkin-header-content {
        margin: 0;
        padding: 0;
        font-size: 22px;
    }
}
</style>
