import {defineComponent} from 'vue';
import readingProgressService from '../services/reading-progress.js';

export default defineComponent({
    name: 'PageReadingProgress',
    props: {
        pageId: {
            type: Number,
            required: true
        },
        bookId: {
            type: Number,
            default: null
        },
        chapterId: {
            type: Number,
            default: null
        },
        autoSave: {
            type: Boolean,
            default: true
        },
        saveInterval: {
            type: Number,
            default: 3000
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
            saveTimeout: null,
            trackingInterval: null,
            visibilityTimeout: null,
            hasUnsavedChanges: false,
            readingStartTime: null,
            readingSessions: []
        };
    },
    computed: {
        progressPercentage() {
            return Math.round(this.progress);
        },
        formattedTime() {
            const totalSeconds = this.timeSpent;
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;
            
            if (hours > 0) {
                return `${hours}h ${minutes}m ${seconds}s`;
            } else if (minutes > 0) {
                return `${minutes}m ${seconds}s`;
            } else {
                return `${seconds}s`;
            }
        },
        readingStatus() {
            if (this.isCompleted) return '已完成';
            if (this.progress > 80) return '即将完成';
            if (this.progress > 50) return '阅读中';
            if (this.progress > 0) return '开始阅读';
            return '未开始';
        }
    },
    async mounted() {
        await this.initializeReadingProgress();
        this.setupEventListeners();
        this.startTracking();
    },
    beforeUnmount() {
        this.cleanup();
    },
    methods: {
        async initializeReadingProgress() {
            this.isLoading = true;
            try {
                const data = await readingProgressService.getProgress(this.pageId);
                this.progress = data.progress_percentage || 0;
                this.scrollPosition = data.scroll_position || 0;
                this.timeSpent = data.time_spent_seconds || 0;
                this.isCompleted = data.is_completed || false;
                this.lastSaved = data.last_read_at ? new Date(data.last_read_at) : null;
                
                if (this.scrollPosition > 0) {
                    this.restoreReadingPosition();
                }
                
                this.readingStartTime = new Date();
            } catch (error) {
                console.error('Failed to initialize reading progress:', error);
                this.readingStartTime = new Date();
            } finally {
                this.isLoading = false;
            }
        },
        
        setupEventListeners() {
            window.addEventListener('scroll', this.handleScroll, { passive: true });
            window.addEventListener('beforeunload', this.handleBeforeUnload);
            document.addEventListener('visibilitychange', this.handleVisibilityChange);
            
            // Handle page navigation
            window.addEventListener('popstate', this.handleNavigation);
            
            // Handle keyboard shortcuts
            document.addEventListener('keydown', this.handleKeyboard);
        },
        
        removeEventListeners() {
            window.removeEventListener('scroll', this.handleScroll);
            window.removeEventListener('beforeunload', this.handleBeforeUnload);
            document.removeEventListener('visibilitychange', this.handleVisibilityChange);
            window.removeEventListener('popstate', this.handleNavigation);
            document.removeEventListener('keydown', this.handleKeyboard);
        },
        
        startTracking() {
            this.startTime = Date.now();
            
            this.trackingInterval = setInterval(() => {
                this.updateReadingMetrics();
            }, 1000);
            
            // Track reading sessions
            this.readingSessions.push({
                startTime: new Date(),
                endTime: null,
                scrollPositions: []
            });
        },
        
        stopTracking() {
            if (this.trackingInterval) {
                clearInterval(this.trackingInterval);
            }
            
            if (this.visibilityTimeout) {
                clearTimeout(this.visibilityTimeout);
            }
            
            if (this.saveTimeout) {
                clearTimeout(this.saveTimeout);
            }
            
            // Close current reading session
            if (this.readingSessions.length > 0) {
                const currentSession = this.readingSessions[this.readingSessions.length - 1];
                if (!currentSession.endTime) {
                    currentSession.endTime = new Date();
                }
            }
        },
        
        updateReadingMetrics() {
            this.calculateReadingProgress();
            this.updateTimeSpent();
            this.scheduleAutoSave();
        },
        
        calculateReadingProgress() {
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
            
            // Smooth progress updates
            this.progress = Math.max(this.progress, currentProgress);
            
            // Update completion status
            if (this.progress >= 95 && !this.isCompleted) {
                this.isCompleted = true;
                this.$emit('reading-completed', {
                    pageId: this.pageId,
                    progress: this.progress,
                    timeSpent: this.timeSpent
                });
            }
        },
        
        updateTimeSpent() {
            if (this.startTime) {
                const currentTime = Date.now();
                const additionalTime = Math.floor((currentTime - this.startTime) / 1000);
                this.timeSpent += additionalTime;
                this.startTime = currentTime;
            }
        },
        
        handleScroll() {
            this.scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
            
            // Track scroll positions for analytics
            if (this.readingSessions.length > 0) {
                const currentSession = this.readingSessions[this.readingSessions.length - 1];
                currentSession.scrollPositions.push({
                    position: this.scrollPosition,
                    timestamp: new Date()
                });
            }
        },
        
        handleVisibilityChange() {
            if (document.hidden) {
                // Page is hidden, save immediately
                this.immediateSave();
                
                // Pause tracking after a delay
                this.visibilityTimeout = setTimeout(() => {
                    this.stopTracking();
                }, 30000); // 30 seconds
            } else {
                // Page is visible, resume tracking
                if (this.visibilityTimeout) {
                    clearTimeout(this.visibilityTimeout);
                }
                
                if (!this.trackingInterval) {
                    this.startTracking();
                }
            }
        },
        
        handleBeforeUnload(event) {
            if (this.hasUnsavedChanges) {
                event.preventDefault();
                event.returnValue = '';
                
                // Force save before unload
                this.immediateSave();
            }
        },
        
        handleNavigation() {
            this.immediateSave();
        },
        
        handleKeyboard(event) {
            // Ctrl/Cmd + Shift + R to reset progress
            if ((event.ctrlKey || event.metaKey) && event.shiftKey && event.key === 'R') {
                event.preventDefault();
                this.resetReadingProgress();
            }
            
            // Ctrl/Cmd + Shift + S to save immediately
            if ((event.ctrlKey || event.metaKey) && event.shiftKey && event.key === 'S') {
                event.preventDefault();
                this.immediateSave();
            }
        },
        
        restoreReadingPosition() {
            setTimeout(() => {
                window.scrollTo({
                    top: this.scrollPosition,
                    behavior: 'smooth'
                });
            }, 100);
        },
        
        scheduleAutoSave() {
            if (!this.autoSave) return;
            
            if (this.saveTimeout) {
                clearTimeout(this.saveTimeout);
            }
            
            this.hasUnsavedChanges = true;
            this.saveTimeout = setTimeout(() => {
                this.saveReadingProgress();
            }, this.saveInterval);
        },
        
        async saveReadingProgress() {
            if (this.isSaving || !this.hasUnsavedChanges) return;
            
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
                this.hasUnsavedChanges = false;
                
                this.$emit('progress-saved', {
                    pageId: this.pageId,
                    data: data,
                    savedAt: this.lastSaved
                });
            } catch (error) {
                console.error('Failed to save reading progress:', error);
                this.$emit('save-error', error);
            } finally {
                this.isSaving = false;
            }
        },
        
        async immediateSave() {
            if (this.saveTimeout) {
                clearTimeout(this.saveTimeout);
            }
            await this.saveReadingProgress();
        },
        
        async resetReadingProgress() {
            if (!confirm('确定要重置阅读进度吗？此操作不可撤销。')) return;
            
            this.progress = 0;
            this.scrollPosition = 0;
            this.timeSpent = 0;
            this.isCompleted = false;
            this.hasUnsavedChanges = true;
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
            await this.immediateSave();
            
            this.$emit('progress-reset', { pageId: this.pageId });
        },
        
        async markAsCompleted() {
            this.isCompleted = true;
            this.progress = 100;
            this.hasUnsavedChanges = true;
            
            await this.immediateSave();
        },
        
        cleanup() {
            this.removeEventListeners();
            this.stopTracking();
            
            if (this.saveTimeout) {
                clearTimeout(this.saveTimeout);
            }
            
            // Final save on cleanup
            if (this.hasUnsavedChanges) {
                this.immediateSave();
            }
        }
    },
    template: `
        <div class="page-reading-progress">
            <div class="reading-progress-ui" :class="{ 'loading': isLoading }">
                <div class="progress-header">
                    <div class="progress-info">
                        <span class="progress-percentage">{{ progressPercentage }}%</span>
                        <span class="progress-status">{{ readingStatus }}</span>
                        <span class="reading-time">{{ formattedTime }}</span>
                    </div>
                    <div class="progress-actions">
                        <button 
                            v-if="!isCompleted"
                            @click="markAsCompleted"
                            class="btn btn-small btn-success"
                            :disabled="isSaving"
                        >
                            标记完成
                        </button>
                        <button 
                            @click="resetReadingProgress"
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
                        :class="{ 'completed': isCompleted }"
                    ></div>
                    <div class="progress-bar-background"></div>
                </div>
                
                <div class="progress-details">
                    <div class="save-status">
                        <small v-if="lastSaved">
                            最后保存: {{ lastSaved.toLocaleString() }}
                        </small>
                        <small v-else-if="isSaving">
                            保存中...
                        </small>
                        <small v-else>
                            未保存
                        </small>
                    </div>
                    
                    <div class="reading-tips">
                        <small>
                            <kbd>Ctrl+Shift+R</kbd> 重置 | <kbd>Ctrl+Shift+S</kbd> 立即保存
                        </small>
                    </div>
                </div>
            </div>
        </div>
    `
});