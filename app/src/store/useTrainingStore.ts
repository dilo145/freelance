import api from '@/services/WebService';
import { Organism } from '@/types/Organism';
import { Student } from '@/types/Student';
import { Training } from '@/types/Training';
import { defineStore } from 'pinia';
import { onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';

export const useTrainingStore = defineStore('training', () => {
  const trainings = ref<Training[]>();
  const students = ref<Student[]>([]);
  const organisms = ref<Organism[]>([]);
  const router = useRouter();
  const isEditing = ref(false);
  const headers = ref<any[]>([
    {
      title: 'ID',
      align: 'start',
      sortable: false,
      value: 'id',
    },
    { title: 'Organism', value: 'organism.name' },
    { title: 'Nom', value: 'name' },
    { title: 'Goal', value: 'goalTraining' },
    { title: 'Actions', key: 'actions', sortable: false },
  ]);

  const newTraining = reactive<Training>({
    id: 0,
    organism: {
      id: 0,
      name: '',
      logo: '',
      created_by: 0,
    },
    name: '',
    goalTraining: '',
  });

  const editTraining = ref<Training>({
    id: 0,
    organism: {
      id: 0,
      name: '',
      logo: '',
      created_by: 0,
    },
    name: '',
    goal_training: '',
  });

  function getTrainings() {
    api
      .get<Training[]>('trainings')
      .then((data) => {
        trainings.value = data;
      })
      .catch((err) => {
        console.error('Error fetching training:', err);
      });
  }

  function getTraining(id: string) {
    api
      .get<Training>(`/trainings/${id}`)
      .then((data) => {
        editTraining.value = data;
      })
      .catch((err) => {
        console.error('Error fetching training:', err);
      });
  }

  function createTraining() {
    api
      .post<Training>('/trainings/new', newTraining)
      .then((response) => {})
      .catch((err) => {
        console.log(err);
      });
  }

  function updateTraining(id: string) {
    api
      .put<Training>('trainings/update', parseInt(id), editTraining.value)
      .then((response) => {
        editTraining.value = response;
        isEditing.value = false;
      })
      .catch((err) => {
        console.log(err);
      });
  }

  function deleteTraining(id: number) {
    api
      .delete<Training>('trainings/delete', id)
      .then(() => {
        getTrainings();
      })
      .catch((err) => {
        console.error('Error deleting training:', err);
      });
  }

  function getStudents() {
    api
      .get<Student[]>('students')
      .then((data) => {
        students.value = data;
      })
      .catch((err) => {
        console.error('Error fetching students:', err);
      });
  }

  function getOrganisms() {
    api
      .get<Organism[]>('organisms')
      .then((data) => {
        organisms.value = data;
      })
      .catch((err) => {
        console.error('Error fetching levels:', err);
      });
  }

  onMounted(() => {
    getTrainings();
  });

  return {
    trainings,
    headers,
    newTraining,
    editTraining,
    isEditing,
    organisms,
    students,
    getTrainings,
    getStudents,
    getOrganisms,
    createTraining,
    deleteTraining,
    getTraining,
    updateTraining,
  };
});
