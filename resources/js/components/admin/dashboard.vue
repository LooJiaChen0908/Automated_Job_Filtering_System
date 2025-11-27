<template>
    <div>
        <h2>Dashboard Page</h2>

        <Loading v-if="loading" />

        <div class="d-flex align-items-center gap-2" v-else>
          
            <div class="card" @click="goTo('company')">
                <div class="card-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="card-info">
                    <h3>Companies</h3>
                    <p>{{ countCompany }}</p>
                </div>
            </div>

            <div class="card" @click="goTo('job')">
                <div class="card-icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="card-info">
                    <h3>Jobs</h3>
                    <p>{{ countJob }}</p>
                    <div class="d-flex align-items-center gap-2 justify-content-center">
                        <div class="circle-wrapper bg-green">
                            {{ activeJob }}
                        </div>
                        <div class="circle-wrapper bg-red">
                            {{ inactiveJob }}
                        </div>
                    </div>
                    <!-- <span class="text-success">{{ activeJob }}</span> | <span class="text-danger">{{inactiveJob}}</span> -->
                   
                    <!-- how many active how many inactive -->
                </div>
            </div>

            <div class="card" @click="goTo('application')">
                <div class="card-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="card-info">
                    <h3>Application</h3>
                    <p>{{ countApplication }}</p>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';
import Loading from '@/components/loading.vue';

export default {
    name: 'Dashboard',
    components: { 
        Loading,
    },
    setup() {
        const router = useRouter();
        const loading = ref(false);
        const countCompany = ref(null);
        const countJob = ref(null);
        const activeJob = ref(null);
        const inactiveJob = ref(null);
        const countApplication = ref(null);

        const getData = async () => {
            loading.value = true;
            try {
                const response = await axios.get('/api/admin/getDashboardData');
                
                countCompany.value = response.data.count_company;
                countJob.value = response.data.count_job;
                activeJob.value = response.data.active_job;
                inactiveJob.value = response.data.inactive_job;
                countApplication.value = response.data.count_application;

            } catch (error) {
                console.error("Failed to fetch data:", error);
            } finally {
                loading.value = false;
            }
        };

        onMounted(() => {
           getData();
        });

        const goTo = async (page) => {
            router.push(`/admin/${page}`);
        }

        return {
           countCompany,
           countJob,
           loading,
           activeJob,
           inactiveJob,
           countApplication,
           goTo,
        };
    }
};
</script>

<style scoped>

    .card {
        background-color: #f9f9f9;
        border-radius: 8px;
        padding: 1rem;
        width: 150px;
        text-align: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card-icon {
        font-size: 2rem;
        color: #4CAF50;
        margin-bottom: 0.5rem;
    }

    .card-info h3 {
        margin: 0;
        font-size: 1rem;
        color: #555;
    }

    .card-info p {
        margin: 0;
        font-size: 1.5rem;
        font-weight: bold;
        color: #333;
    }

    .card:hover{
        cursor: pointer;
    }

    .circle-wrapper {
        width: 25px; 
        height: 25px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
    }

    .bg-green{
        background-color: #198754;
    }

    .bg-red{
        background-color: #dc3545;
    }

</style>