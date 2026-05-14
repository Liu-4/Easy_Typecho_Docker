// amaps.js 最终版（适配 Typecho + Swup，保留所有原有功能）
let mapInstance = null; // 全局保存地图实例，换页时销毁
let mapControlElements = []; // 保存地图控件，便于销毁

// 1. 【新增】全局销毁旧地图（Swup换页前必须执行，释放容器）
window.destroyOldAmap = function() {
    console.log('🔴 销毁旧地图实例');
    // 销毁地图实例（关键：避免重复实例占用容器）
    if (mapInstance && mapInstance.destroy) {
        mapInstance.destroy();
        mapInstance = null;
    }
    // 清除所有控件
    mapControlElements.forEach(control => {
        if (mapInstance && mapInstance.removeControl) mapInstance.removeControl(control);
    });
    mapControlElements = [];
    // 清除容器样式（避免残留全屏/加载状态）
    const mapEl = document.querySelector('#cover-map');
    if (mapEl) mapEl.classList.remove('loaded', 'full-screen');
};

// 2. 【核心修改】全局初始化地图（支持外部传参，不依赖window.MapData）
// 调用时需传入：{ amap_key: '密钥', locate: '经纬度', locale: '地点名' }
window.initAmap = function(customMapData) {
    console.log('🟢 开始初始化地图，参数：', customMapData);
    const mapsArea = document.querySelector('#cover-map');
    
    // 第一步：校验容器（必须有且尺寸正常）
    if (!mapsArea) {
        console.error('❌ 地图容器 #cover-map 不存在（检查模板是否有重复/缺失）');
        return;
    }
    const style = window.getComputedStyle(mapsArea);
    if (style.width === '0px' || style.height === '0px') {
        console.warn('⚠️ 地图容器尺寸为0，设置默认尺寸');
        mapsArea.style.width = '100%';
        mapsArea.style.height = '400px';
    }

    // 第二步：校验配置（密钥+定位数据必须有）
    if (!customMapData || !customMapData.amap_key) {
        console.error('❌ 缺少高德地图API密钥 (amap_key)');
        return;
    }
    const mapConfig = customMapData;
    if (!mapConfig.locate) {
        console.warn('⚠️ 缺少定位数据，用默认北京坐标');
    }

    // 第三步：加载高德API并初始化地图（保留你原有的所有功能）
    AMapLoader.load({
        key: mapConfig.amap_key,
        version: "2.0",
    })
    .then((AMap)=>{
        console.log('✅ 高德地图API加载成功');
        
        // 【关键优化】坐标处理（兼容空格+简化校验，避免解析失败）
        let defaultCenter = [116.39748, 39.90882]; // 默认北京（兜底）
        let locateData = mapConfig.locate || '';
        console.log('📌 原始定位数据:', locateData);
        
        // 只清理首尾空格，不删其他字符（避免误删有效内容）
        locateData = locateData.trim();
        console.log('📌 清理后定位数据:', locateData);
        
        // 解析坐标（允许逗号前后有空格，比如 "118.793, 25.353"）
        if (locateData) {
            const coords = locateData.split(',').map(item => item.trim());
            if (coords.length === 2) {
                const lng = parseFloat(coords[0]);
                const lat = parseFloat(coords[1]);
                // 只要是有效数字就用（高德会自动处理范围）
                if (!isNaN(lng) && !isNaN(lat)) {
                    defaultCenter = [lng, lat];
                    console.log('✅ 使用正确坐标:', defaultCenter);
                } else {
                    console.warn('⚠️ 坐标转换失败，用默认值');
                }
            } else {
                console.warn('⚠️ 坐标格式错误（需逗号分隔），用默认值');
            }
        }

        try {
            // 初始化地图（用全局mapInstance保存，便于销毁）
            mapInstance = new AMap.Map('cover-map',{
                center: defaultCenter,
                zoom: 15.5,
                viewMode: '3D',
                resizeEnable: true,
                wallColor: 'rgba(175,206,233,0.2)',
                roofColor: 'rgba(175,206,233,0.5)'
            });
            console.log('✅ 地图实例创建成功');

            // 地图完全渲染后创建标记点（保留你的原有逻辑）
            mapInstance.on('complete', () => {
                console.log('✅ 地图完全加载完成');
                const mapCenter = mapInstance.getCenter();
                
                if (mapCenter && !isNaN(mapCenter.lng) && !isNaN(mapCenter.lat)) {
                    // 创建标记点（保留测试标记+自定义标记）
                    try {
                        const testMarker = new AMap.Marker({
                            position: mapCenter,
                            map: mapInstance
                        });
                        console.log('✅ 测试标记点创建成功');
                        
                        const marker = new AMap.Marker({
                            content: '<i class="thyuu-icon-locate"></i>',
                            position: mapCenter,
                            anchor: 'bottom-center',
                            label: {
                                direction: 'bottom',
                                content: `${mapConfig.locale || ''}<a class="button thyuu-icon-new-window" title="在高德地图APP里打开" target="_blank" href="https://uri.amap.com/marker?position=${mapCenter.lng},${mapCenter.lat}&callnative=1"></a>`
                            },
                            map: mapInstance
                        });
                        console.log('✅ 自定义标记点创建成功');

                        marker.on('click', () => {
                            mapInstance.setZoomAndCenter(19, mapCenter, false, 500);
                        });
                    } catch (markerError) {
                        console.error('❌ 创建标记点失败:', markerError);
                        // 备用方法（保留）
                        try {
                            const marker = new AMap.Marker();
                            marker.setMap(mapInstance);
                            marker.setPosition(mapCenter);
                            marker.setContent('<i class="thyuu-icon-locate"></i>');
                            console.log('✅ 备用方法创建标记点成功');
                        } catch (backupError) {
                            console.error('❌ 备用方法也失败:', backupError);
                        }
                    }
                }

                // 主题切换（保留）
                const updateMapStyle = () => {
                    const theme = document.documentElement.getAttribute("theme") || "light";
                    mapInstance.setMapStyle(`amap://styles/${theme === "light" ? 'whitesmoke' : 'grey'}`);
                };
                updateMapStyle();
                document.getElementById('theme-toggle')?.addEventListener('click', updateMapStyle);

                // 全屏切换（保留）
                const toolbar = document.querySelector('.amap-control.amap-toolbar');
                let toolsBtn;
                if (toolbar) {
                    toolsBtn = document.createElement('span');
                    toolbar.appendChild(toolsBtn);
                    toolsBtn.className = 'thyuu-icon-full-screen';
                }
                const coverBtn = document.querySelector('#cover-map-btn');
                const toggleFullScreen = (btn) => {
                    if (!btn) return;
                    if (mapsArea.classList.contains('full-screen')) {
                        mapsArea.classList.remove('full-screen');
                        btn.className = 'thyuu-icon-full-screen';
                        mapInstance.setZoom(16, false, 500);
                        mapInstance.setPitch(0, false, 500);
                    } else {
                        mapsArea.classList.add('full-screen');
                        btn.className = 'thyuu-icon-exit-screen';
                        mapInstance.setZoom(18, false, 500);
                        mapInstance.setPitch(50, false, 500);
                    }
                    setTimeout(() => mapInstance.resize(), 300);
                };
                if (toolsBtn) toolsBtn.onclick = () => toggleFullScreen(toolsBtn);
                if (coverBtn) coverBtn.onclick = () => toggleFullScreen(toolsBtn);

                // 加载完成样式（保留）
                setTimeout(() => {
                    mapsArea.classList.add('loaded');
                }, 500);
                mapInstance.setZoom(16, false, 1000);
            });

            // 添加控件（保留，同时保存控件用于销毁）
            mapInstance.plugin(["AMap.ToolBar", "AMap.Scale", "AMap.MapType"], function () {
                [AMap.MapType, AMap.Scale, AMap.ToolBar].forEach(Control => {
                    try {
                        const control = new Control();
                        mapInstance.addControl(control);
                        mapControlElements.push(control); // 保存控件
                    } catch (e) {
                        console.error('❌ 添加控件失败:', e);
                    }
                });
            });	
        } catch (mapError) {
            console.error('❌ 地图初始化失败:', mapError);
        }
    })
    .catch((loadError) => {
        console.error('❌ 加载地图API失败:', loadError);
    });
};

// 3. 【初始化】页面首次刷新时执行（从data属性拿数据，不用window.MapData）
document.addEventListener('DOMContentLoaded', function () {
    console.log('🔄 页面首次加载，初始化地图');
    // 从模板的data属性拿当前页面数据（和Swup换页保持一致）
    const mapContainer = document.querySelector('.map_content[data-location]');
    const initialData = {
        amap_key: 'aa2d52429a3391141e64331edeb646d6', // 你的密钥（固定）
        locate: mapContainer ? mapContainer.dataset.location : '', // 从data拿定位
        locale: mapContainer ? mapContainer.dataset.locale : ''     // 从data拿地点名
    };
    // 初始化地图
    window.initAmap(initialData);
});