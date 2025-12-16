<template>
    <div>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2>Interview Slots</h2>
        </div>
        
        <Loading v-if="loading" />

        <div v-else>
            <div class="card mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    Zoom Meeting
                </div>

                <div class="card-body">
                    <div class="form-group mb-2">
                        <label class="form-label">Topic:</label>
                        <input type="text" class="form-control" v-model="form.topic">
                        <span v-if="validationErrors.topic" class="text-danger">{{ validationErrors.topic[0] }}</span>
                    </div>
                
                    <div class="form-group">
                        <label class="form-label">Application:</label>
                        <v-select :options="applications" v-model="form.application_id" label="label" :reduce="option => option.value" placeholder="Select an application"></v-select>
                        <span v-if="validationErrors.application_id" class="text-danger">{{ validationErrors.application_id[0] }}</span>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <button class="btn btn-primary" @click="createMeeting" :disabled="isCreate">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" v-if="isCreate"></span>
                        Create Meeting
                    </button>
                </div>
            </div>
            
            <FullCalendar :options="calendarOptions">
                <template v-slot:eventContent="arg">
                    <div class="slot">
                        <span class="dot"></span>
                    
                        <div class="info">
                            <b>{{ arg.event.title }}</b>
                            <div class="mt-1">{{ arg.timeText }}</div>
                        </div>
                    </div>
                </template>
            </FullCalendar>
        </div>

    </div>
</template>

<script>
import { ref, reactive, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';
import Loading from '@/components/loading.vue';
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'

export default {
    name: 'Interview',
    components: { 
        Loading,
    },
    setup() {
        const router = useRouter();
        const loading = ref(false);
        const confirmedInterview = ref([]);
        const isCreate = ref(false);
        const applications = ref([]);
        const validationErrors = reactive({});

        const form = reactive({
            topic: 'Interview with Candidate',
            application_id: '',
        });

        const createMeeting = async () => {
            isCreate.value = true;

            try {    
                const response = await axios.post('/api/admin/createMeeting', form).then(res => {
                    // window.open(res.data.join_url)
                });

                Swal.fire({
                    title: 'Meeting Created successfully',
                    icon: 'success',
                    confirmButtonColor: '#007bff',
                    confirmButtonText: 'Ok'
                });

            } catch (error) {
                console.error("There was an error creating meetings:", error);

                if (error.response?.status === 422) {
                    // Clear old errors
                    Object.keys(validationErrors).forEach(key => delete validationErrors[key]);
                    // Assign new errors
                    Object.assign(validationErrors, error.response.data.errors);
                } else {
                    Swal.fire({
                        title: 'Failed!',
                        text: error.response?.data?.message || 'Something went wrong',
                        icon: 'error'
                    });
                }

            } finally {
                isCreate.value = false;
            }
        };

        const getData = async () => {
            loading.value = true;
            try {
                const response = await axios.get('/api/admin/application/getConfirmedInterview');
                confirmedInterview.value = response.data.confirmed_interview;
                applications.value = response.data.applications;

            } catch (error) {
                console.error("There was an error fetching applications:", error);
            } finally {
                loading.value = false;
            }
        };

        onMounted(() => {
            getData();
        });

        const calendarOptions = computed(() => ({
            plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: confirmedInterview.value,
            eventTimeFormat: { // show time in month view
                hour: 'numeric',
                minute: '2-digit',
                meridiem: 'short' // gives "9am"
            },
            dateClick(info) {
                info.view.calendar.changeView('timeGridDay', info.date)
            },
        }))

        return {
           loading,
           calendarOptions,
           confirmedInterview,
           createMeeting,
           form,
           applications,
           isCreate,
           validationErrors
        };
    }
};
</script>

<style scoped>
    .slot{
        display: flex; 
        align-items: flex-start; 
        gap: 6px; 
        white-space: normal;
    }

    .dot{
        width: 8px; 
        height: 8px; 
        border-radius: 50%; 
        margin-top: 4px;
        background-color: #3788d8;
    }

    .info{
        font-size: 12px; 
        line-height: 1.2;
    }
</style>


