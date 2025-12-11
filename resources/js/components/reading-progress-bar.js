import {defineComponent} from 'vue';
import readingProgressService from '../services/reading-progress.js';

export default defineComponent({
    name: 'ReadingProgressBar',
    props: {
        pageId: {
            type: Number,
            required: true
        },
        autoSave: {
            type: Boolean,
            default: true
        },
        saveInterval: {
            type: Number,
            default: 3000 // 3 seconds
        }
    },
    data() {
        return {
            progress: 0,
            scrollPosition: 0,
            timeSpent: 0,
            isCompleted: false,
            lastSaved: null,
            isLoading: false,
            isSaving: false,
            startTime: null,
            saveTimeout: null
        };
    },
    computed: {
        progressPercentage() {
            return Math.round(this.progress);
        },
        formattedTime() {
            const minutes = Math.floor(this.timeSpent / 60);
            const seconds = this.timeSpent % 60;
            return `${minutes}:${seconds.toString().padStart(2, '0')}`;
        },
        progressBarClass() {
            return {
                'reading-progress-bar': true,
                'completed': this.isCompleted,
                'loading': this.isLoading
            };
        }
    },
    async mounted() {
        await this.loadProgress();
        this.startTime = Date.now();
        this.startTracking();
        
        window.addEventListener('scroll', this.handleScroll);
        window.addEventListener('beforeunload', this.handleBeforeUnload);
    },
    beforeUnmount() {
        this.stopTracking();
        window.removeEventListener('scroll', this.handleScroll);
        window.removeEventListener('beforeunload', this.handleBeforeUnload);
        
        if (this.saveTimeout) {
            clearTimeout(this.saveTimeout);
        }
    },
    methods: {
        async loadProgress() {
            this.isLoading = true;
            try {
                const data = await readingProgressService.getProgress(this.pageId);
                this.progress = data.progress_percentage || 0;
                this.scrollPosition = data.scroll_position || 0;
                this.timeSpent = data.time_spent_seconds || 0;
                this.isCompleted = data.is_completed || false;
                this.lastSaved = data.last_read_at ? new Date(data.last_read_at) : null;
                
                if (this.scrollPosition > 0) {
                    this.restoreScrollPosition();
                }
            } catch (error) {
                console.error('Failed to load reading progress:', error);
            } finally {
                this.isLoading = false;
            }
        },
        
        startTracking() {
            this.trackingInterval = setInterval(() => {
                this.updateTimeSpent();
                this.calculateProgress();
            }, 1000);
        },
        
        stopTracking() {
            if (this.trackingInterval) {
                clearInterval(this.trackingInterval);
            }
        },
        
        handleScroll() {
            this.scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
            this.scheduleSave();
        },
        
        updateTimeSpent() {
            if (this.startTime) {
                this.timeSpent = Math.floor((Date.now() - this.startTime) / 1000) + 
                               (this.timeSpent || 0);
                this.startTime = Date.now();
            }
        },
        
        calculateProgress() {
            const windowHeight = window.innerHeight;
            const documentHeight = Math.max(
                document.body.scrollHeight,
                document.body.offsetHeight,
                document.documentElement.clientHeight,
                document.documentElement.scrollHeight,
                document.documentElement.offsetHeight
            );
            
            const scrollableHeight = documentHeight - windowHeight;
            const currentProgress = scrollableHeight > 0 
                ? Math.min(100, (this.scrollPosition / scrollableHeight) * 100)
                : 0;
            
            this.progress = Math.max(this.progress, currentProgress);
            
            if (this.progress >= 95 && !this.isCompleted) {
                this.isCompleted = true;
            }
        },
        
        restoreScrollPosition() {
            setTimeout(() => {
                window.scrollTo({
                    top: this.scrollPosition,
                    behavior: 'smooth'
                });
            }, 100);
        },
        
        scheduleSave() {
            if (!this.autoSave) return;
            
            if (this.saveTimeout) {
                clearTimeout(this.saveTimeout);
            }
            
            this.saveTimeout = setTimeout(() => {
                this.saveProgress();
            }, this.saveInterval);
        },
        
        async saveProgress() {
            if (this.isSaving) return;
            
            this.isSaving = true;
            try {
                const data = {
                    progress_percentage: this.progress,
                    scroll_position: this.scrollPosition,
                    time_spent_seconds: this.timeSpent,
                    is_completed: this.isCompleted
                };
                
                await readingProgressService.updateProgress(this.pageId, data);
                this.lastSaved = new Date();
            } catch (error) {
                console.error('Failed to save reading progress:', error);
            } finally {
                this.isSaving = false;
            }
        },
        
        async handleBeforeUnload() {
            if (this.autoSave) {
                await this.saveProgress();
            }
        },
        
        async markAsCompleted() {
            this.isCompleted = true;
            this.progress = 100;
            await this.saveProgress();
        },
        
        async resetProgress() {
            this.progress = 0;
            this.scrollPosition = 0;
            this.timeSpent = 0;
            this.isCompleted = false;
            window.scrollTo({ top: 0, behavior: 'smooth' });
            await this.saveProgress();
        }
    },
    template: `
        <div :class="progressBarClass">
            <div class="reading-progress-header">
                <div class="progress-info">
                    <span class="progress-text">{{ progressPercentage }}% 已读</span>
                    <span class="time-text">{{ formattedTime }}</span>
                </div>
                <div class="progress-actions">
                    <button 
                        v-if="!isCompleted"
                        @click="markAsCompleted"
                        class="btn btn-small"
                        :disabled="isSaving"
                    >
                        标记为已读
                    </button>
                    <button 
                        @click="resetProgress"
                        class="btn btn-small btn-secondary"
                        :disabled="isSaving"
                    >
                        重置
                    </button>
                </div>
            </div>
            <div class="progress-bar-container">
                <div 
                    class="progress-bar-fill" 
                    :style="{ width: progress + '%' }"
                ></div>
                <div class="progress-bar-background"></div>
            </div>
            <div v-if="lastSaved" class="save-status">
                <small>最后保存: {{ lastSaved.toLocaleTimeString() }}</small>
            </div>
        </div>
    `
});