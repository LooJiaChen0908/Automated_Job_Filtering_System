<template>
    <div>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2>Interview Slots</h2>
        </div>
        
        <Loading v-if="loading" />

        <div v-else>
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

        const getData = async () => {
            loading.value = true;
            try {
                const response = await axios.get('/api/admin/application/getConfirmedInterview');
                confirmedInterview.value = response.data.confirmed_interview;

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


