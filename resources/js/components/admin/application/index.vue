<template>
    <div>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h2>Application List</h2>
        </div>

        <Loading v-if="loading" />

        <div class="card mb-2" v-else>
            <div class="card-header d-flex align-items-center justify-content-between">
                Filter By:
                <i class="fas fa-chevron-down" style="cursor: pointer;"></i>
            </div>

            <div class="card-body">
                <div class="d-flex align-items-center gap-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="salary" v-model="criteria.salary">
                        <label class="form-check-label" for="flexCheckDefault">
                            Salary
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="experience" v-model="criteria.experience">
                        <label class="form-check-label" for="experience">
                            Experience
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="specialization"  v-model="criteria.specialization">
                        <label class="form-check-label" for="specialization">
                            Specialization
                        </label>
                    </div>
                    <div class="form-check">
                        <!-- <input class="form-check-input" type="checkbox" id="work_type"  v-model="criteria.work_type">
                        <label class="form-check-label" for="work_type">
                            Work Type
                        </label> -->
                    </div>

                    <!-- <v-select
                        :options="specializations"
                        v-model="criteria.selected_specialization"
                        label="label"
                        :reduce="option => option.value"
                        placeholder="Select specialization"
                    /> -->

                </div>
            </div>

            <div class="card-footer d-flex justify-content-end gap-2">
                <button class="btn btn-secondary" @click="reset">Reset</button>
                <button class="btn btn-primary d-flex justify-content-end" @click="filterApplication">Submit</button>
            </div>
        </div>

        <div v-if="!loading && !applications.length">
            <p v-if="filtered">No applications match your filter criteria. Try adjusting the filters.</p>
            <p v-else>No applications available. Please add some.</p>
        </div>

        <div v-if="applications.length && !loading">

            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Job Detail</th>
                        <th scope="col">Applicant Info</th>
                        <th scope="col">Resume</th>
                        <th scope="col">Interview Schedule</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(application, index) in applications" :key="application.id">
                        <th scope="row">{{ index + 1 }}</th>
                        <td>
                            <div v-if="application.job">
                                <span class="text-capitalize">{{ application.job.title }}</span> <span v-if="application.job.company && application.job.company.state">- {{ application.job.company.state }}</span>
                            </div>
                            <p>ID: {{application.id}}</p>
                            <!-- {{ application.job.description }} -->

                            <p>JOB ID: {{application.job_id}}</p>
                            <p>Work experience: {{application.job.required_experience_years}}</p>
                            <p>Salary min {{application.job.salary_min}}</p>
                            <p>Specialization {{application.job.specialization}}</p>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1 mb-1">
                                {{ application.applicant.email }} <button class="btn btn-secondary btn-sm" @click="sendEmail(application.applicant.email)"><i class="fas fa-envelope"></i></button>
                            </div>

                            <div class="d-flex align-items-center gap-1">
                                {{ application.applicant.contact_no }} <button class="btn btn-info btn-sm ms-1" @click="call(application.applicant.contact_no)"><i class="fas fa-phone"></i></button>
                            </div>
                            <div>
                                Expected Salary: {{application.applicant.expected_salary}}
                            </div>
                            <div>
                                Work experience: {{application.applicant.work_experience}}
                            </div>
                            <div>
                                Specialization: {{application.applicant.specialization}}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <template v-if="application.resume_path.endsWith('.pdf')">
                                    <a :href="`/storage/${application.resume_path}`" target="_blank" class="btn btn-info btn-sm ms-1">
                                        <!-- <i class="fas fa-eye">View</i> -->
                                        View
                                    </a>
                                </template>
                                <!-- <template v-else>
                                <a :href="`/storage/${application.resume_path}`" download class="btn btn-warning btn-sm ms-1">
                                    <i class="fas fa-download"></i> Download
                                </a>
                                </template> -->
                                <a 
                                    v-if="application.resume_path" 
                                    :href="`/storage/${application.resume_path}`" 
                                    download 
                                    class="btn btn-warning btn-sm ms-1 d-flex align-items-center gap-1"
                                    target="_blank"
                                >
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        </td>
                        <td>
                            <!-- Mode -->
                            <div>
                                <strong>Mode:</strong> {{ application.interview_mode === 'online' ? 'Online' : 'Face-to-Face' }}
                            </div>

                            <!-- Proposed Slots -->
                            <div v-if="application.interview_slots && application.interview_slots.length">
                                <strong>Proposed:</strong>&nbsp;
                                <span v-for="(slot,index) in application.interview_slots" :key="index">
                                    {{ $moment(slot).format('YYYY MMM DD HH:mm A') }}
                                    <span v-if="$moment(slot).format('YYYY-MM-DD HH:mm:ss') === $moment(application.selected_slot).format('YYYY-MM-DD HH:mm:ss')" class="text-success">
                                        <i class="fas fa-check-circle ms-1"></i>
                                    </span>
                                    <span v-if="index < application.interview_slots.length - 1">, </span>
                                </span>
                            </div>

                            <!-- Selected Slot -->
                            <div v-if="application.selected_slot">
                                <strong class="mb-2">Selected:</strong>
                                <div class="d-flex align-items-center gap-2">
                                    {{ $moment(application.selected_slot).format('YYYY MMM DD HH:mm A') }}
                                    <button class="btn btn-sm btn-outline-success" v-if="application.interview_status !== 2" @click="confirmSchedule(application.id, application.selected_slot)" :disabled="isConfirm">
                                        <i class="fas fa-check me-1"></i>Confirm
                                    </button>
                                </div>
                            </div>

                            <!-- Suggested Slots -->
                            <div v-if="application.suggested_slots && application.suggested_slots.length">
                                <strong class="mb-2">Suggested:</strong>
                                <div class="d-flex align-items-center gap-2 mb-2" v-for="(slot,index) in application.suggested_slots" :key="index">
                                    {{ $moment(slot).format('YYYY MMM DD HH:mm A') }}
                                    <button class="btn btn-sm btn-outline-success" v-if="application.interview_status !== 2" @click="confirmSchedule(application.id, slot)" :disabled="isConfirm">
                                        <i class="fas fa-check me-1"></i>Confirm
                                    </button>
                                </div>
                            </div>

                            <!-- Confirmed Slot -->
                            <div v-if="application.confirmed_slot">
                                <strong>Confirmed:</strong>&nbsp;
                                <span class="text-success fw-bold">
                                    {{ $moment(application.confirmed_slot).format('YYYY MMM DD HH:mm A') }}
                                </span>
                            </div>

                            <!-- Interview Status Badge -->
                            <div>
                                <span v-if="application.interview_status === 0" class="badge bg-warning">Pending</span>
                                <span v-else-if="application.interview_status === 1" class="badge bg-info">Awaiting Admin</span>
                                <span v-else-if="application.interview_status === 2" class="badge bg-success">Confirmed</span>
                            </div>
                        </td>
                        <td>
                            <span class="text-warning" v-if="application.status == 0">Pending</span>
                            <span class="text-success" v-else-if="application.status == 1">Approved</span>
                            <span class="text-success" v-else-if="application.status == 2">Shortlisted</span>
                            <span class="text-danger" v-else>Rejected</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-primary" @click="updateStatus(application, 'matched')" v-if="application.status != 1">Mark as Matched</button>
                                <!-- <button class="btn btn-success" @click="editApplication(application)"><i class="fas fa-edit"></i>&nbsp;Edit / Approve</button> -->
                                <!-- <button class="btn btn-danger" @click="deleteApplication(application)"><i class="fas fa-trash-alt"></i>&nbsp;Delete</button> -->
                                <!-- <button class="btn btn-info">Notify User</button> -->
                                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modal" v-if="!application.interview_mode" @click="openSchedule(application.id)">Schedule Interview</button>
                                <!-- application detail -->
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Schedule Interview</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Select interview mode:</p>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="online" value="online" v-model="interviewForm.interviewMode">
                            <label class="form-check-label" for="online">Online</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="f2f" value="f2f" v-model="interviewForm.interviewMode">
                            <label class="form-check-label" for="f2f">Face-to-Face</label>
                        </div>
                        <hr>
                        <div v-for="(slot, index) in interviewForm.interviewSlots" :key="index">
                            <VueDatePicker
                            v-model="interviewForm.interviewSlots[index]"
                            :min-date="new Date()"
                            :placeholder="`Select time slot ${index + 1}`"
                            />
                        </div>

                         <!-- Time slot 1:
                        <VueDatePicker v-model="interviewForm.date1" :min-date="new Date()" placeholder="Select time slot 1"></VueDatePicker>
                         Time slot 2:
                        <VueDatePicker v-model="interviewForm.date2" :min-date="new Date()" placeholder="Select time slot 2"></VueDatePicker>
                         Time slot 3:
                        <VueDatePicker v-model="interviewForm.date3" :min-date="new Date()" placeholder="Select time slot 3"></VueDatePicker> -->

                        <!-- <VueDatePicker v-model="test" multiDates /> -->

                        <!-- <p>Select a time slot:</p>
                        <div v-for="day in slots" :key="day.date" class="mb-3">
                            <strong>Date: {{ day.date }}</strong>
                            <div class="d-flex gap-2 mt-2">
                                <button
                                v-for="time in day.times"
                                :key="time"
                                class="btn btn-outline-primary"
                                :class="{ active: interviewForm.selectedSlot === `${day.date} ${time}` }"
                                @click="interviewForm.selectedSlot = `${day.date} ${time}`"
                                >
                                {{ time }}
                                </button>
                            </div>
                        </div> -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" @click="updateSchedule" :disabled="isSubmit">Confirm</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import { ref, reactive, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import Swal from 'sweetalert2';
import Loading from '@/components/loading.vue';

export default {
    name: 'Application',
    components: { 
        Loading,
    },
    setup() {
        const router = useRouter();
        const applications = ref([]);
        const loading = ref(false);
        const copied = ref({ index: null, field: null });
        const specializations = ref([]);
        const isSubmit = ref(false);
        const isConfirm = ref(false);

        const filtered = ref(false);

        const criteria = reactive({
            salary: false,
            experience: false,
            specialization: false,
            work_type: false,
            selected_specialization: null
        });

        const interviewForm = ref({
            applicationId: '',
            interviewMode: '',
            interviewSlots: [null, null, null]
        });

        const filterApplications = ref([]);

        const getData = async () => {
            loading.value = true;
            try {
                const response = await axios.get('/api/admin/application');
                applications.value = response.data.data;
                specializations.value = response.data.specializations;

            } catch (error) {
                console.error("There was an error fetching applications:", error);
            } finally {
                loading.value = false;
            }
        };

        const openSchedule = async (id) => {
            interviewForm.value.applicationId = id
            interviewForm.value.interviewMode = ''
            interviewForm.value.interviewSlots = [null, null, null]
        }

        const slots = ref([])

        const generateSlots = () => {
            // const today = new Date()

            // for (let i = 0; i < 7; i++) {
            //     const date = new Date(today)
            //     date.setDate(today.getDate() + i)

            //     const formattedDate = date.toISOString().split('T')[0] // YYYY-MM-DD

            //     slots.value.push({
            //         date: formattedDate,
            //         times: ['10:00 AM', '01:00 PM', '03:00 PM']
            //     })
            // }

            const today = new Date()
            slots.value = [] // reset

            for (let i = 0; i < 7; i++) {
                const date = new Date(today)
                date.setDate(today.getDate() + i)

                const formattedDate = date.toISOString().split('T')[0] // YYYY-MM-DD

                // Define your slot times
                const slotTimes = ['10:00 AM', '01:00 PM', '03:00 PM']

                // If it's today, filter out past times
                let validTimes = slotTimes
                if (i === 0) {
                    const now = new Date()

                    validTimes = slotTimes.filter(time => {
                        // Convert slot string to a Date object for comparison
                        const [hours, minutesPart] = time.split(':')
                        const minutes = minutesPart.slice(0, 2)
                        const ampm = minutesPart.slice(3)

                        let slotHour = parseInt(hours, 10)
                        if (ampm === 'PM' && slotHour !== 12) slotHour += 12
                        if (ampm === 'AM' && slotHour === 12) slotHour = 0

                        const slotDateTime = new Date(date)
                        slotDateTime.setHours(slotHour, parseInt(minutes), 0, 0)

                        return slotDateTime > now
                    })
                }

                // Only push if there are valid times left
                if (validTimes.length > 0) {
                    slots.value.push({
                        date: formattedDate,
                        times: validTimes
                    })
                }
            }
        }

        onMounted(() => {
            getData();
            // generateSlots();
        });

        const closeModal = () => { // still has problem warning
            const modalEl = document.getElementById('modal')

            // Restore focus to the trigger button
            const triggerBtn = document.querySelector('[data-bs-target="#modal"]')
            if (triggerBtn) triggerBtn.focus()

            const modal = bootstrap.Modal.getInstance(modalEl)
            modal.hide()

            document.body.classList.remove('modal-open')
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove())
        }

        const updateSchedule = async () => {
            if (isSubmit.value) return;

            isSubmit.value = true;

            try {
                const payload = interviewForm.value // unwrap the ref

                const interviewSlots = payload.interviewSlots.filter(slot => slot);

                const response = await axios.post(`/api/admin/application/${payload.applicationId}/updateSchedule`, {
                    interview_mode: payload.interviewMode,
                    interview_slots: interviewSlots,
                })

                closeModal();
                getData();
                
                Swal.fire({
                    title: 'Schedule Updated successfully',
                    icon: 'success',
                    confirmButtonColor: '#007bff',
                    confirmButtonText: 'Ok'
                });

            } catch (error) {
                console.error("There was an error updating interview schedule:", error); 
            } finally {
                isSubmit.value = false;
            }
        }

        const confirmSchedule = async (application_id, slot) => {
            if (isConfirm.value) return;

            isConfirm.value = true;

            try {
                const response = await axios.post(`/api/admin/application/${application_id}/confirmSchedule`, { confirmed_slot: slot });

                Swal.fire({
                    title: 'Schedule Confirmed successfully',
                    icon: 'success',
                    confirmButtonColor: '#007bff',
                    confirmButtonText: 'Ok'
                });

            } catch (error) {
                console.error("There was an error confirming interview schedule:", error); 
            } finally {
                isConfirm.value = false;
            }
        };

        const deleteApplication = async () => {
            
        };

        const editApplication = async () => {
            
        };   

        const updateStatus = async (application) => {
            try {
                const response = await axios.post(`/api/admin/application/${application.id}/updateStatus`, {});

                Swal.fire({
                    title: 'Updated successfully',
                    icon: 'success',
                    confirmButtonColor: '#007bff',
                    confirmButtonText: 'Ok'
                });

            } catch (error) {
                console.error("There was an error updating application:", error);
            }
        };

        const call = async (phoneNo) => {
            window.location.href = `tel:${phoneNo}`;
        };
        
        const sendEmail = async (email) => {
            if (!email) {
                Swal.fire('No email address found', '', 'warning');
                return;
            }

            // Create a mailto link
            const subject = encodeURIComponent("Job Application Follow-up");
            const body = encodeURIComponent("Hello,\n\nI would like to discuss your application.\n\nRegards,");
            window.location.href = `mailto:${email}?subject=${subject}&body=${body}`;
        };

        const download = async () => {

        };

        const filterApplication = async () => {
            filtered.value = true;

            try {
                const response = await axios.post(`/api/admin/application/filter`, {
                    check_salary: criteria.salary,
                    check_experience: criteria.experience,
                    check_specialization: criteria.specialization,
                    check_work_type: criteria.work_type,
                });

                applications.value = response.data.filterApplications

            } catch (error) {
                console.error("There was an error filtering applications:", error);
            }
        };

        // watch(criteria, (newVal) => {
        //     if (!newVal.salary && !newVal.experience && !newVal.specialization) {
        //         getData();
        //     }
        // }, { deep: true });

        const reset = async () => {
            criteria.salary = false;
            criteria.experience = false;
            criteria.specialization = false;
            criteria.work_type = false;
            filtered.value = false;

            getData();
        };

        return {
           applications,
           loading,
           editApplication,
           deleteApplication,
           call,
           sendEmail,
           download,
           criteria,
           filterApplication,
           reset,
           filterApplications,
           filtered,
           specializations,
           updateStatus,
           updateSchedule,
           slots,
           interviewForm,
           isSubmit,
           isConfirm,
           openSchedule,
           confirmSchedule,
        };
    }
};
</script>

<style>
/* Add some global styles here if needed */
</style>
