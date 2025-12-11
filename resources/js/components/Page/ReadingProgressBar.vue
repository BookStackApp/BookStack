<template>
  <div class="reading-progress-container">
    <div
      class="reading-progress-bar"
      :style="{ width: progressPercentage + '%' }"
      :aria-valuenow="progressPercentage"
      aria-valuemin="0"
      aria-valuemax="100"
    ></div>
    <span class="progress-percentage">{{ progressPercentage }}%</span>
  </div>
</template>

<script>
export default {
  name: 'ReadingProgressBar',
  props: {
    pageId: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      progressPercentage: 0,
      lastSavedPosition: 0,
      saveTimeout: null,
      isLoading: false
    };
  },
  mounted() {
    this.loadReadingProgress();
    window.addEventListener('scroll', this.handleScroll);
    window.addEventListener('beforeunload', this.saveProgress);
  },
  beforeUnmount() {
    window.removeEventListener('scroll', this.handleScroll);
    window.removeEventListener('beforeunload', this.saveProgress);
    clearTimeout(this.saveTimeout);
  },
  methods: {
    async loadReadingProgress() {
      try {
        this.isLoading = true;
        const response = await axios.get(`/api/reading-progress/${this.pageId}`);
        if (response.data && response.data.last_read_position) {
          this.progressPercentage = response.data.progress_percentage || 0;
          // 如果有保存的位置，滚动到该位置
          if (response.data.last_read_position > 0) {
            window.scrollTo(0, response.data.last_read_position);
          }
        }
      } catch (error) {
        console.error('Failed to load reading progress:', error);
      } finally {
        this.isLoading = false;
      }
    },
    handleScroll() {
      // 计算阅读进度百分比
      const windowHeight = window.innerHeight;
      const documentHeight = Math.max(
        document.body.scrollHeight,
        document.body.offsetHeight,
        document.documentElement.clientHeight,
        document.documentElement.scrollHeight,
        document.documentElement.offsetHeight
      );
      const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      const totalScrollable = documentHeight - windowHeight;
      const percentage = totalScrollable > 0 ? Math.round((scrollTop / totalScrollable) * 100) : 0;

      this.progressPercentage = percentage;

      // 防抖处理，避免频繁保存
      clearTimeout(this.saveTimeout);
      this.saveTimeout = setTimeout(() => {
        this.saveProgress(percentage, scrollTop);
      }, 1000);
    },
    async saveProgress(percentage, scrollTop) {
      // 避免不必要的保存
      if (Math.abs(percentage - this.lastSavedPercentage) < 1 && Math.abs(scrollTop - this.lastSavedPosition) < 100) {
        return;
      }

      try {
        await axios.post('/api/reading-progress', {
          page_id: this.pageId,
          progress_percentage: percentage,
          last_read_position: scrollTop,
          is_completed: percentage >= 100
        });

        this.lastSavedPercentage = percentage;
        this.lastSavedPosition = scrollTop;
      } catch (error) {
        console.error('Failed to save reading progress:', error);
      }
    }
  }
};
</script>

<style scoped>
.reading-progress-container {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 4px;
  background-color: #f0f0f0;
  z-index: 1000;
}

.reading-progress-bar {
  height: 100%;
  background-color: #2196f3;
  transition: width 0.3s ease;
}

.progress-percentage {
  position: absolute;
  top: 4px;
  right: 20px;
  background-color: #2196f3;
  color: white;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 12px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>