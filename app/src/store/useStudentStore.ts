import api from '@/services/WebService';
import { Student } from '@/types/Student';
import { defineStore } from 'pinia';
import { onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';

export const useStudentStore = defineStore('student', () => {
  const students = ref<Student[]>([]);
  const isEditing = ref(false);
  const router = useRouter();
  const headers = ref<any[]>([
    {
      title: 'Id',
      align: 'start',
      sortable: false,
      value: 'id',
    },
    {
      title: 'Firstname',
      value: 'firstName',
    },
    { title: 'Lastname', value: 'lastName' },
    { title: 'Email', value: 'email' },
    { title: 'Individual', value: 'invidual' },
    { title: 'Actions', key: 'actions', sortable: false },
  ]);

  const newStudent = reactive<Student>({
    id: 0,
    invidual: true,
    registrations: [],
    answer: [],
    firstName: '',
    lastName: '',
    email: '',
    photo: '',
    userIdentifier: '',
    roles: [],
    password: '',
    createdAt: '',
    updatedAt: '',
    deletedAt: '',
    messagesSended: [],
    messagesRecived: [],
  });

  const editStudent = ref<Student>({
    id: 0,
    invidual: true,
    registrations: [],
    answer: [],
    firstName: '',
    lastName: '',
    email: '',
    photo: '',
    userIdentifier: '',
    roles: [],
    password: '',
    createdAt: '',
    updatedAt: '',
    deletedAt: '',
    messagesSended: [],
    messagesRecived: [],
  });

  function getStudent(id: string) {
    api
      .get<Student>(`students/${id}`)
      .then((data) => {
        editStudent.value = data;
      })
      .catch((err) => {
        console.error('Error fetching lesson:', err);
      });
  }

  function getStudents() {
    return new Promise((resolve, reject) => {
      api
        .get<Student[]>('students')
        .then((data) => {
          // students.value = data;
          resolve(data);
        })
        .catch((err) => {
          console.error('Error fetching students:', err);
        });
    });
  }

  function getTrainingStudent(id: string) {
    api
      .get<Student[]>(`users/getByTraining/${id}`)
      .then((data) => {
        students.value = data;
      })
      .catch((err) => {
        console.error('Error fetching training:', err);
      });
  }
  function createStudent() {
    api
      .post<Student>('students/new', newStudent)
      .then(() => {
        router.push('/students');
      })
      .catch((err) => {
        console.log(err);
      });
  }

  function updateStudent(id: string) {
    api
      .put<Student>('students/edit', parseInt(id), editStudent.value)
      .then(() => {
        getStudent(id);
        isEditing.value = false;
      })
      .catch((err) => {
        console.log(err);
      });
  }

  function deleteStudent(id: number) {
    api
      .delete<Student>('students/delete', id)
      .then(() => {
        getStudents();
        router.push('/students');
      })
      .catch((err) => {
        console.error('Error deleting lesson:', err);
      });
  }

  onMounted(() => {
    getStudents();
  });

  return {
    students,
    newStudent,
    editStudent,
    headers,
    isEditing,
    getStudent,
    getStudents,
    getTrainingStudent,
    updateStudent,
    createStudent,
    deleteStudent,
  };
});
