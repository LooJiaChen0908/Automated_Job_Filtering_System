<template>
    <!-- <h1>TESTING</h1> -->
    <div id="app">
        <router-view></router-view>
    </div>
</template>

<script>
import { ref, provide, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

export default {
    name: 'App',
    setup() {
        

        return {
            
        };
    }
};
</script>

<style>
/* Add some global styles here if needed */
</style>
