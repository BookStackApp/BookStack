import {defineComponent} from 'vue';
import readingProgressService from '../services/reading-progress.js';

export default defineComponent({
    name: 'ChapterNavigation',
    props: {
        bookId: {
            type: Number,
            required: true
        },
        currentChapterId: {
            type: Number,
            default: null
        },
        currentPageId: {
            type: Number,
            default: null
        },
        chapters: {
            type: Array,
            required: true
        },
        showProgress: {
            type: Boolean,
            default: true
        }
    },
    data() {
        return {
            chapterProgress: {},
            pageProgress: {},
            loading: false,
            error: null,
            expandedChapters: new Set(),
            navigationMode: 'tree' // 'tree' or 'list'
        };
    },
    computed: {
        organizedChapters() {
            if (!this.chapters || this.chapters.length === 0) return [];
            
            return this.chapters.map(chapter => ({
                ...chapter,
                progress: this.chapterProgress[chapter.id] || 0,
                pages: chapter.pages?.map(page => ({
                    ...page,
                    progress: this.pageProgress[page.id] || 0,
                    isCompleted: this.pageProgress[page.id] >= 100
                })) || [],
                completedPages: (chapter.pages || []).filter(
                    page => this.pageProgress[page.id] >= 100
                ).length,
                totalPages: (chapter.pages || []).length
            }));
        },
        
        bookProgress() {
            if (!this.chapters || this.chapters.length === 0) return 0;
            
            const totalPages = this.chapters.reduce((sum, chapter) => 
                sum + (chapter.pages?.length || 0), 0
            );
            
            const completedPages = this.chapters.reduce((sum, chapter) => 
                sum + (chapter.pages?.filter(page => 
                    this.pageProgress[page.id] >= 100
                ).length || 0), 0
            );
            
            return totalPages > 0 ? Math.round((completedPages / totalPages) * 100) : 0;
        },
        
        navigationSummary() {
            const totalPages = this.chapters.reduce((sum, chapter) => 
                sum + (chapter.pages?.length || 0), 0
            );
            
            const completedPages = this.chapters.reduce((sum, chapter) => 
                sum + (chapter.pages?.filter(page => 
                    this.pageProgress[page.id] >= 100
                ).length || 0), 0
            );
            
            return {
                totalChapters: this.chapters.length,
                totalPages,
                completedPages,
                completionRate: totalPages > 0 ? Math.round((completedPages / totalPages) * 100) : 0
            };
        }
    },
    async mounted() {
        await this.loadProgress();
    },
    watch: {
        bookId() {
            this.loadProgress();
        },
        chapters: {
            handler() {
                this.loadProgress();
            },
            deep: true
        }
    },
    methods: {
        async loadProgress() {
            if (!this.bookId || !this.chapters || this.chapters.length === 0) return;
            
            this.loading = true;
            this.error = null;
            
            try {
                const progressData = await readingProgressService.getUserProgress();
                
                // Build page progress map
                this.pageProgress = {};
                progressData.forEach(item => {
                    this.pageProgress[item.page_id] = item.progress_percentage || 0;
                });
                
                // Calculate chapter progress
                this.calculateChapterProgress();
                
            } catch (error) {
                console.error('Failed to load chapter progress:', error);
                this.error = error.message || '加载章节进度失败';
            } finally {
                this.loading = false;
            }
        },
        
        calculateChapterProgress() {
            this.chapterProgress = {};
            
            this.chapters.forEach(chapter => {
                if (!chapter.pages || chapter.pages.length === 0) {
                    this.chapterProgress[chapter.id] = 0;
                    return;
                }
                
                const totalProgress = chapter.pages.reduce((sum, page) => 
                    sum + (this.pageProgress[page.id] || 0), 0
                );
                
                this.chapterProgress[chapter.id] = Math.round(
                    totalProgress / chapter.pages.length
                );
            });
        },
        
        toggleChapter(chapterId) {
            if (this.expandedChapters.has(chapterId)) {
                this.expandedChapters.delete(chapterId);
            } else {
                this.expandedChapters.add(chapterId);
            }
        },
        
        isChapterExpanded(chapterId) {
            return this.expandedChapters.has(chapterId);
        },
        
        getProgressIcon(progress) {
            if (progress >= 100) return 'icon-check-circle';
            if (progress >= 75) return 'icon-circle-three-quarters';
            if (progress >= 50) return 'icon-circle-half';
            if (progress >= 25) return 'icon-circle-quarter';
            return 'icon-circle-empty';
        },
        
        getProgressColor(progress) {
            if (progress >= 100) return '#10b981';
            if (progress >= 75) return '#3b82f6';
            if (progress >= 50) return '#8b5cf6';
            if (progress >= 25) return '#f59e0b';
            return '#6b7280';
        },
        
        getProgressLabel(progress) {
            if (progress >= 100) return '已完成';
            if (progress >= 75) return '即将完成';
            if (progress >= 50) return '阅读中';
            if (progress >= 25) return '开始阅读';
            return '未开始';
        },
        
        navigateToPage(pageId) {
            this.$emit('navigate-to-page', pageId);
        },
        
        navigateToChapter(chapterId) {
            this.$emit('navigate-to-chapter', chapterId);
        },
        
        refreshProgress() {
            this.loadProgress();
        },
        
        formatProgress(progress) {
            return Math.round(progress);
        }
    },
    template: `
        <div class="chapter-navigation" :class="{ 'show-progress': showProgress }">
            <div class="navigation-header">
                <div class="header-content">
                    <h3>章节导航</h3>
                    <div class="progress-summary" v-if="showProgress">
                        <div class="book-progress">
                            <div class="progress-bar">
                                <div 
                                    class="progress-fill" 
                                    :style="{ width: bookProgress + '%', backgroundColor: getProgressColor(bookProgress) }"
                                ></div>
                            </div>
                            <span>{{ bookProgress }}% 完成</span>
                        </div>
                        <div class="summary-stats">
                            <span>{{ navigationSummary.completedPages }}/{{ navigationSummary.totalPages }} 页面</span>
                            <span>{{ navigationSummary.totalChapters }} 章节</span>
                        </div>
                    </div>
                </div>
                <div class="navigation-controls">
                    <button 
                        @click="refreshProgress"
                        class="btn btn-small btn-icon"
                        title="刷新进度"
                        :disabled="loading"
                    >
                        <i class="icon icon-refresh"></i>
                    </button>
                </div>
            </div>
            
            <div v-if="loading" class="loading-state">
                <div class="loading-spinner"></div>
                <p>加载章节进度中...</p>
            </div>
            
            <div v-else-if="error" class="error-state">
                <i class="icon icon-warning"></i>
                <p>{{ error }}</p>
                <button @click="refreshProgress" class="btn btn-small">重试</button>
            </div>
            
            <div v-else class="chapters-list">
                <div 
                    v-for="chapter in organizedChapters" 
                    :key="chapter.id" 
                    class="chapter-item"
                    :class="{ 
                        'current': chapter.id === currentChapterId,
                        'completed': chapter.progress >= 100
                    }"
                >
                    <div class="chapter-header" @click="toggleChapter(chapter.id)">
                        <div class="chapter-info">
                            <i 
                                class="icon chapter-icon" 
                                :class="getProgressIcon(chapter.progress)"
                                :style="{ color: getProgressColor(chapter.progress) }"
                            ></i>
                            <span class="chapter-name">{{ chapter.name }}</span>
                        </div>
                        <div class="chapter-meta">
                            <span class="chapter-progress">
                                {{ formatProgress(chapter.progress) }}%
                            </span>
                            <i 
                                class="icon icon-chevron-down" 
                                :class="{ 'expanded': isChapterExpanded(chapter.id) }"
                            ></i>
                        </div>
                    </div>
                    
                    <div 
                        v-if="isChapterExpanded(chapter.id)" 
                        class="chapter-pages"
                        :class="{ 'expanded': isChapterExpanded(chapter.id) }"
                    >
                        <div 
                            v-for="page in chapter.pages" 
                            :key="page.id" 
                            class="page-item"
                            :class="{ 
                                'current': page.id === currentPageId,
                                'completed': page.isCompleted
                            }"
                            @click="navigateToPage(page.id)"
                        >
                            <div class="page-info">
                                <i 
                                    class="icon page-icon" 
                                    :class="getProgressIcon(page.progress)"
                                    :style="{ color: getProgressColor(page.progress) }"
                                ></i>
                                <span class="page-name">{{ page.name }}</span>
                            </div>
                            <div class="page-meta">
                                <span class="page-progress">
                                    {{ getProgressLabel(page.progress) }}
                                </span>
                                <span class="page-time" v-if="page.reading_time">
                                    {{ formatDuration(page.reading_time) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="navigation-footer">
                <div class="legend">
                    <div class="legend-item">
                        <i class="icon icon-check-circle" style="color: #10b981"></i>
                        <span>已完成</span>
                    </div>
                    <div class="legend-item">
                        <i class="icon icon-circle-three-quarters" style="color: #3b82f6"></i>
                        <span>进行中</span>
                    </div>
                    <div class="legend-item">
                        <i class="icon icon-circle-empty" style="color: #6b7280"></i>
                        <span>未开始</span>
                    </div>
                </div>
            </div>
        </div>
    `
});