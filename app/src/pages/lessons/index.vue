<script setup lang="ts">
import DataTableActions from "@/components/lessons/DataTableActions.vue";
import { useLessonStore } from "@/store/useLessonStore";
import { ref } from "vue";
import { useRouter } from "vue-router";

const lessonStore = useLessonStore();
const router = useRouter();
const isDeleteModalOpen = ref(false);
const id = ref<number>(0);

function onDeleteValidate(id: number) {
  lessonStore.deleteLesson(id);
  isDeleteModalOpen.value = false;
}
</script>

<template>
  <h1>Lessons</h1>

  <DataTableActions />

  <v-data-table
    :items="lessonStore.lessons"
    :headers="lessonStore.headers"
    class="elevation-1 mt-6"
  >
    <template v-slot:item.actions="{ item }">
      <v-icon
        class="me-2"
        size="small"
        color="secondary"
        @click="router.push(`/lessons/${item.id}`)"
      >
        mdi-pencil
      </v-icon>
      <v-icon
        size="small"
        color="red"
        @click="
          isDeleteModalOpen = true;
          id = item.id;
        "
      >
        mdi-delete
      </v-icon>
    </template>
  </v-data-table>

  <v-dialog v-model="isDeleteModalOpen" width="500">
    <v-card>
      <v-card-title class="text-center"
        >Are you sure you want to delete this lesson?</v-card-title
      >
      <v-card-actions class="justify-end">
        <v-btn @click="isDeleteModalOpen = false">Cancel</v-btn>
        <v-btn color="red" @click="onDeleteValidate(id)">Delete</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>