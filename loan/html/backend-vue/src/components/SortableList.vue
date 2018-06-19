<template>
  <div>
    <slot></slot>
  </div>
</template>

<script>
import Sortable from 'sortablejs';

export default {
  data() {
    return {
    };
  },
  props: {
    list: Array,
  },
  mounted() {
    Sortable.create(this.$el, {
      onUpdate: (e) => {
        this.list.splice(e.newIndex, 0, this.list.splice(e.oldIndex, 1)[0]);
        this.$emit('reorder', {
          oldIndex: e.oldIndex,
          newIndex: e.newIndex,
        });
      },
    });
  },
};
</script>

<style scoped>
.handle {
  cursor: move;
  cursor: -webkit-grabbing;
}
</style>
