<script setup lang="ts">
import DataTableActions from "@/components/classes/DataTableActions.vue";
import { useTrainingStore } from "@/store/useTrainingStore";
import { ref } from "vue";
import { useRouter } from "vue-router";

const search = ref();
const router = useRouter();
const trainingStore = useTrainingStore();

const isDeleteModalOpen = ref(false);
const id = ref(0);

function onDeleteValidate(id: number) {
  trainingStore.deleteTraining(id);
  isDeleteModalOpen.value = false;
}

defineProps({
  fromOrganism: {
    type: Boolean,
    required: false,
    default: false,
  },
  listeFromOrganism: {
    type: Array,
    required: false,
    default: [],
  },
});
</script>

<template>
  <h1>Liste des classes</h1>
  <DataTableActions v-if="!fromOrganism" />
  <v-data-table
    :items="fromOrganism ? listeFromOrganism : trainingStore.trainings"
    :headers="trainingStore.headers"
    class="elevation-1 mt-6"
  >
    <template v-slot:item.actions="{ item }">
      <v-icon
        class="me-2"
        size="small"
        @click="
          router.push(`/classes/${item.id}`);
          trainingStore.isEditing = false;
        "
      >
      </v-icon>
      <v-icon
        class="me-2"
        size="small"
        @click="
          router.push(`/classes/${item.id}`);
          trainingStore.isEditing = false;
        "
      >
        mdi-eye
      </v-icon>
      <v-icon
        class="me-2"
        size="small"
        color="secondary"
        @click="
          router.push(`/classes/${item.id}`);
          trainingStore.isEditing = true;
        "
      >
      </v-icon>
      <v-icon
        class="me-2"
        size="small"
        color="secondary"
        @click="
          router.push(`/classes/${item.id}`);
          trainingStore.isEditing = true;
        "
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

    <!-- No data message -->

    <template v-slot:no-data>
      <div class="text-subtitle">No levels found. Please add a new item.</div>
    </template>
  </v-data-table>

  <!-- Delete confirmation dialog -->
  <v-dialog v-model="isDeleteModalOpen" width="500">
    <v-card>
      <v-card-title class="text-center"
        >Are you sure you want to delete this training?</v-card-title
      >
      <v-card-actions class="justify-end">
        <v-btn @click="isDeleteModalOpen = false">Cancel</v-btn>
        <v-btn color="red" @click="onDeleteValidate(id)">Delete</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
