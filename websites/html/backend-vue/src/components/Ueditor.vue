<template>
  <div>
    <script :id="randomId"  type="text/plain" style="width: 800px;height: 350px;"></script>
  </div>
</template>

<script>
  /* global UE:false */

  export default {
    data() {
      return {
        randomId: `editor_${(Math.random() * 100000000000000000)}`,
        instance: null,
      };
    },
    mounted() {
      this.createScript('/js/baiduEdutor/ueditor.config.js', 'config');
      this.createScript('/js/baiduEdutor/ueditor.all.js', 'all');
      setTimeout(() => {
        this.initEditor();
      }, 1000);
    },
    beforeDestroy() {
      if (this.instance !== null && this.instance.destroy) {
        this.instance.destroy();
      }
      window.location.reload();
    },
    methods: {
      initEditor() {
        this.$nextTick(() => {
          this.instance = UE.getEditor(this.randomId, this.ueditorConfig);
          this.instance.addListener('ready', () => {
            this.$emit('ready', this.instance);
          });
        });
      },
      createScript(url, id) {
        const head = document.getElementsByTagName('head')[0];
        const script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = url;
        script.id = id;
        head.appendChild(script);
      },
    },
  };
</script>
