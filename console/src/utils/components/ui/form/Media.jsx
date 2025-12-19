import { useDropzone } from 'react-dropzone'
import { useState, useEffect, useRef } from 'react'
import axios from 'axios'
import config from '@/config/configLoader'
import { toast } from 'sonner'
import { HiOutlineExclamationCircle } from 'react-icons/hi'
import { Button } from '@/utils/components/ui/button'
import { Modal, ModalBody, ModalHeader } from '@/utils/components/ui/modal'
import { ResourceIndex } from '@/utils/components/common/ResourceIndex'
import { config as mediaConfig } from '@/mod/media_library/utils/config'
import { HiOutlineDocument, HiOutlinePhotograph, HiMenu } from 'react-icons/hi'
import { DndContext, closestCenter, PointerSensor, useSensor, useSensors } from '@dnd-kit/core'
import {
    SortableContext,
    useSortable,
    verticalListSortingStrategy,
    arrayMove,
} from '@dnd-kit/sortable'
import { CSS } from '@dnd-kit/utilities'
import { restrictToVerticalAxis } from '@dnd-kit/modifiers'
import dayjs from 'dayjs'
import { isImage, getFileBytes } from '@/utils/common'
import { useAxios } from '@/utils/hooks/useAxios'

// 認証付きaxiosインスタンスを作成するヘルパー関数
const createAuthenticatedAxios = () => {
    const apiClient = axios.create({
        baseURL: config.endpointUrl,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
        withCredentials: true,
        xsrfCookieName: 'XSRF-TOKEN',
        xsrfHeaderName: 'X-XSRF-TOKEN',
    })

    const getCookie = (name) => {
        const value = `; ${document.cookie}`
        const parts = value.split(`; ${name}=`)
        if (parts.length === 2) return parts.pop().split(';').shift()
        return null
    }

    // リクエスト前に XSRF-TOKEN を強制的にヘッダへ設定
    apiClient.interceptors.request.use((cfg) => {
        if (!cfg.headers) cfg.headers = {}
        const xsrf = getCookie('XSRF-TOKEN')
        if (xsrf && !cfg.headers['X-XSRF-TOKEN']) {
            try {
                cfg.headers['X-XSRF-TOKEN'] = decodeURIComponent(xsrf)
            } catch (_) {
                cfg.headers['X-XSRF-TOKEN'] = xsrf
            }
        }
        return cfg
    })

    return apiClient
}

/**
 * ファイルアップロード
 */
export default function FileUploader({
    endpoint = ':company_alias/:facility_alias/media_library/store',
    onUploadComplete,
    onUploadStateChange,
    acceptedFileTypes = {
        'image/*': ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.svg'],
        'application/pdf': ['.pdf'],
        'application/msword': ['.doc'],
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document': ['.docx'],
        'application/vnd.ms-excel': ['.xls'],
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': ['.xlsx'],
        'text/plain': ['.txt'],
        'text/csv': ['.csv'],
    },
    maxFileSize = 100 * 1024 * 1024, // 100MB
}) {
    const [progress, setProgress] = useState(0)
    const [uploading, setUploading] = useState(false)
    const [error, setError] = useState(null)
    const apiClient = createAuthenticatedAxios()
    const { replacePath } = useCompanyFacility()

    const CHUNK_SIZE = 2 * 1024 * 1024 // 2MBごとに分割

    // アップロード状態の変更を親に通知
    useEffect(() => {
        if (onUploadStateChange) {
            onUploadStateChange(uploading)
        }
    }, [uploading, onUploadStateChange])

    // ファイル形式を検証する関数
    const validateFileType = (file) => {
        const allowedExtensions = Object.values(acceptedFileTypes).flat()
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase()
        return allowedExtensions.includes(fileExtension)
    }

    // ファイルサイズを検証する関数
    const validateFileSize = (file) => {
        return file.size <= maxFileSize
    }

    const onDrop = async (acceptedFiles = [], rejectedFiles = []) => {
        // 拒否されたファイルがある場合のエラーハンドリング
        if (rejectedFiles.length > 0) {
            const rejectedFile = rejectedFiles[0]
            let errorMessage = ''

            if (rejectedFile.errors[0]?.code === 'file-invalid-type') {
                errorMessage = 'サポートされていないファイル形式です。'
            } else if (rejectedFile.errors[0]?.code === 'file-too-large') {
                errorMessage = `ファイルサイズが大きすぎます。最大${Math.round(
                    maxFileSize / 1024 / 1024
                )}MBまでです。`
            } else {
                errorMessage = 'ファイルの選択に失敗しました。'
            }

            setError(errorMessage)
            return
        }

        if (acceptedFiles.length === 0) return

        const file = acceptedFiles[0]

        // 追加のファイル形式検証
        if (!validateFileType(file)) {
            setError('サポートされていないファイル形式です。')
            return
        }

        // 追加のファイルサイズ検証
        if (!validateFileSize(file)) {
            setError(
                `ファイルサイズが大きすぎます。最大${Math.round(
                    maxFileSize / 1024 / 1024
                )}MBまでです。`
            )
            return
        }

        // エラーをクリア
        setError(null)

        const totalChunks = Math.ceil(file.size / CHUNK_SIZE)
        setUploading(true)
        setProgress(0) // プログレスをリセット

        try {
            for (let i = 0; i < totalChunks; i++) {
                const start = i * CHUNK_SIZE
                const end = Math.min(file.size, start + CHUNK_SIZE)
                const chunk = file.slice(start, end)

                const formData = new FormData()
                formData.append('file', chunk)
                formData.append('fileName', file.name)
                formData.append('chunkIndex', i)
                formData.append('totalChunks', totalChunks)

                await apiClient.post(replacePath(endpoint), formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                })

                setProgress(Math.round(((i + 1) / totalChunks) * 100))
            }

            // アップロード完了
            setUploading(false)
            setProgress(0)

            // 成功トーストを表示
            toast.success('ファイルのアップロードが完了しました')

            // アップロード完了コールバックを実行
            if (onUploadComplete) {
                onUploadComplete()
            }
        } catch (err) {
            setUploading(false)
            setProgress(0)

            // エラーメッセージを設定
            const errorMessage =
                err.response?.data?.message || err.message || 'ファイルのアップロードに失敗しました'
            setError(errorMessage)

            console.error('Upload error:', err)
        }
    }

    const { getRootProps, getInputProps, isDragActive } = useDropzone({
        onDrop,
        accept: acceptedFileTypes,
        maxSize: maxFileSize,
        multiple: false,
    })

    // 許可されるファイル形式の説明文を生成
    const getAcceptedFileTypesDescription = () => {
        const extensions = Object.values(acceptedFileTypes).flat()
        return extensions.join(', ')
    }

    return (
        <div className="w-full mx-auto">
            {/* エラーメッセージ表示 */}
            {error && (
                <div className="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md">
                    <div className="flex items-center">
                        <HiOutlineExclamationCircle className="h-5 w-5 text-red-400 dark:text-red-300 mr-2 flex-shrink-0" />
                        <div className="text-sm text-red-700 dark:text-red-200">{error}</div>
                    </div>
                </div>
            )}

            <div
                {...getRootProps()}
                className={`border-2 border-dashed rounded p-6 text-center cursor-pointer transition-colors ${
                    isDragActive
                        ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                        : error
                        ? 'border-red-300 dark:border-red-600 bg-red-50 dark:bg-red-900/10'
                        : 'border-gray-300 dark:border-gray-600'
                } ${uploading ? 'pointer-events-none opacity-50' : ''}`}
            >
                <input {...getInputProps()} disabled={uploading} />
                {uploading ? (
                    <div className="space-y-2">
                        <p className="text-blue-600 dark:text-blue-400 font-medium">
                            アップロード中...
                        </p>
                        <p className="text-sm text-gray-500 dark:text-gray-400">{progress}% 完了</p>
                    </div>
                ) : isDragActive ? (
                    <div className="space-y-2">
                        <p className="text-blue-600 dark:text-blue-400 font-medium">
                            ここにファイルをドロップしてください
                        </p>
                        <p className="text-sm text-gray-500 dark:text-gray-400">
                            対応形式: {getAcceptedFileTypesDescription()}
                        </p>
                        <p className="text-sm text-gray-500 dark:text-gray-400">
                            最大サイズ: {Math.round(maxFileSize / 1024 / 1024)}MB
                        </p>
                    </div>
                ) : (
                    <div className="space-y-2">
                        <p className="text-gray-700 dark:text-gray-300">
                            ファイルをドラッグ＆ドロップ、またはクリックで選択
                        </p>
                        <p className="text-sm text-gray-500 dark:text-gray-400">
                            対応形式: {getAcceptedFileTypesDescription()}
                        </p>
                        <p className="text-sm text-gray-500 dark:text-gray-400">
                            最大サイズ: {Math.round(maxFileSize / 1024 / 1024)}MB
                        </p>
                        {error && (
                            <p className="text-sm text-red-600 dark:text-red-400">
                                再度お試しください
                            </p>
                        )}
                    </div>
                )}
            </div>

            {uploading && (
                <div className="mt-4 w-full bg-gray-200 dark:bg-gray-700 rounded">
                    <div
                        className="bg-blue-500 text-xs leading-none py-1 text-center text-white rounded transition-all duration-300"
                        style={{ width: `${progress}%` }}
                    >
                        {progress}%
                    </div>
                </div>
            )}
        </div>
    )
}

import { useCallback } from 'react'
import { useCompanyFacility } from '@/utils/context/CompanyFacilityContext'

// メディアアイテムの状態管理ロジックをカスタムフックに抽出
const useMediaItem = (defaultValue) => {
    const [selected, setSelected] = useState(
        defaultValue && typeof defaultValue === 'object' ? defaultValue : null
    )
    const [initializing, setInitializing] = useState(!!defaultValue)
    const { data: showData, sendRequest: fetchMediaShow } = useAxios()
    const { replacePath } = useCompanyFacility()

    useEffect(() => {
        if (defaultValue && typeof defaultValue === 'object') {
            if (selected?.id != defaultValue.id) {
                setSelected(defaultValue)
            }
            setInitializing(false)
            return
        }
        if (!defaultValue) {
            setSelected(null)
            setInitializing(false)
            return
        }
        if (typeof defaultValue !== 'object') {
            // IDが渡された場合、現在のselectedと異なれば取得
            if (!selected || selected.id != defaultValue) {
                setInitializing(true)
                fetchMediaShow({
                    method: 'get',
                    url: `${replacePath(mediaConfig.end_point)}/${defaultValue}`,
                })
            } else {
                // 既に正しいアイテムが選択されている
                setInitializing(false)
            }
        }
    }, [defaultValue, fetchMediaShow, selected])

    useEffect(() => {
        const item = showData?.payload?.data
        if (item) {
            setSelected(item)
        }
        if (showData) {
            setInitializing(false)
        }
    }, [showData])

    return { initializing, selected, setSelected }
}

// 共通のレンダリングロジックをコンポーネントに分離
const getFileIcon = (mimeType) => {
    if (isImage(mimeType)) return <HiOutlinePhotograph className="w-6 h-6 text-blue-500" />
    return <HiOutlineDocument className="w-6 h-6 text-gray-500" />
}

const SelectedItemDisplay = ({ selected, onClear, readonly = false }) => (
    <div className="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
        {isImage(selected?.mime_type) ? (
            <img
                src={selected?.file_url}
                alt={selected?.file_name}
                className="w-12 h-12 object-cover rounded border"
            />
        ) : (
            <div className="w-12 h-12 flex items-center justify-center bg-gray-100 rounded border">
                {getFileIcon(selected?.mime_type)}
            </div>
        )}
        <div>
            <div className="font-medium">
                <a href={selected.file_url} target="_blank" rel="noopener noreferrer">
                    {selected?.file_name}
                </a>
            </div>
            <div className="text-xs text-gray-500">
                {selected?.mime_type} ・ {getFileBytes(selected?.file_size)}
            </div>
        </div>
        {!readonly && (
            <Button size="xs" color="light" onClick={onClear}>
                クリア
            </Button>
        )}
    </div>
)

const InitializingSkeleton = () => (
    <div className="flex items-center gap-3 w-full animate-pulse">
        <div className="w-12 h-12 rounded border bg-gray-200 dark:bg-gray-700" />
        <div className="flex-1">
            <div className="h-4 bg-gray-200 dark:bg-gray-700 rounded w-40 mb-2" />
            <div className="h-3 bg-gray-200 dark:bg-gray-700 rounded w-24" />
        </div>
    </div>
)

// MediaImage と MediaFile の共通ロジックを担うコンポーネント
const MediaSelector = ({
    defaultValue = null,
    onChange = () => {},
    modalTitle,
    fileUploaderProps = {},
    resourceIndexProps = {},
    readonly = false,
    disabled = false,
}) => {
    const { initializing, selected, setSelected } = useMediaItem(defaultValue)
    const [showModal, setShowModal] = useState(false)
    const [isUploading, setIsUploading] = useState(false)
    const resourceIndexRef = useRef(null)
    const { replacePath } = useCompanyFacility()

    const handleClear = useCallback(() => {
        setSelected(null)
        onChange(null)
    }, [onChange, setSelected])

    const handleSelect = useCallback(
        (item) => {
            setSelected(item)
            onChange(item.id, item)
            setShowModal(false)
        },
        [onChange, setSelected]
    )

    const handleUploadComplete = useCallback(() => {
        resourceIndexRef.current?.refresh()
        toast.success('ファイルをアップロードしました')
    }, [])

    const columns = [
        { key: 'btns', label: '', sortable: false, _props: { style: { width: '8%' } } },
        { key: 'file', label: '', sortable: false, _props: { style: { width: '80px' } } },
        { key: 'file_name', label: 'ファイル名' },
        { key: 'mime_type', label: '形式', _props: { style: { width: '10%' } } },
        { key: 'file_size', label: 'サイズ', _props: { style: { width: '10%' } } },
        { key: 'created_at', label: '作成日', _props: { style: { width: '15%' } } },
    ]

    const addScopedColumns = {
        file: (item, row, idx) => (
            <td key={idx} className="text-center p-2">
                {isImage(item.mime_type) ? (
                    <div className="flex justify-center">
                        <img
                            src={item.file_url}
                            alt={item.file_name}
                            className="w-12 h-12 object-cover rounded border"
                            onError={(e) => {
                                e.target.style.display = 'none'
                                e.target.nextSibling.style.display = 'flex'
                            }}
                        />
                        <div
                            className="w-12 h-12 flex items-center justify-center bg-gray-100 rounded border"
                            style={{ display: 'none' }}
                        >
                            {getFileIcon(item.mime_type)}
                        </div>
                    </div>
                ) : (
                    <div className="flex justify-center">{getFileIcon(item.mime_type)}</div>
                )}
            </td>
        ),
        file_name: (item) => (
            <td>
                <span className="text-gray-700 dark:text-gray-200">{item.file_name}</span>
            </td>
        ),
        file_size: (item, row, idx) => (
            <td className="text-end pe-2" key={idx}>
                {getFileBytes(item.file_size)}
            </td>
        ),
        created_at: (item) => (
            <td className="text-center">{dayjs(item.created_at).format('YYYY/MM/DD HH:mm:ss')}</td>
        ),
        btns: (item, row, idx) => (
            <td key={idx} className="text-end">
                {!readonly && !disabled && item.id !== selected?.id && (
                    <div className="flex justify-center w-full">
                        <Button size="xs" outline onClick={() => handleSelect(item)}>
                            選択
                        </Button>
                    </div>
                )}
            </td>
        ),
    }

    return (
        <div className="space-y-2">
            <div className="flex items-center gap-3">
                {initializing ? (
                    <InitializingSkeleton />
                ) : !selected ? (
                    !readonly &&
                    !disabled && (
                        <Button outline onClick={() => setShowModal(true)} size="xs">
                            メディアライブラリから選択する
                        </Button>
                    )
                ) : (
                    <SelectedItemDisplay
                        selected={selected}
                        onClear={handleClear}
                        readonly={readonly || disabled}
                    />
                )}
            </div>

            <Modal
                show={showModal}
                onClose={() => setShowModal(false)}
                size="5xl"
                dismissible={!isUploading}
            >
                <ModalHeader>{modalTitle}</ModalHeader>
                <ModalBody>
                    <div className="mb-4">
                        <FileUploader
                            endpoint={':company_alias/:facility_alias/media_library/store'}
                            onUploadStateChange={setIsUploading}
                            onUploadComplete={handleUploadComplete}
                            {...fileUploaderProps}
                        />
                    </div>
                    <ResourceIndex
                        ref={resourceIndexRef}
                        options={{
                            breads: [{ name: mediaConfig.name }],
                            config: {
                                ...mediaConfig,
                                path: replacePath(mediaConfig.path),
                                end_point: replacePath(mediaConfig.end_point),
                            },
                            columns,
                            isNew: false,
                            isEdit: false,
                            isDelete: false,
                            skeletonRow: 3,
                            addScopedColumns,
                            ...resourceIndexProps,
                        }}
                    />
                </ModalBody>
            </Modal>
        </div>
    )
}

/**
 * メディアライブラリから画像選択
 */
export const MediaImage = ({ readonly = false, disabled = false, ...props }) => (
    <MediaSelector
        {...props}
        readonly={readonly}
        disabled={disabled}
        modalTitle="メディアを選択"
        fileUploaderProps={{
            acceptedFileTypes: {
                'image/*': ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.svg'],
            },
        }}
        resourceIndexProps={{
            baseParams: { 'criteria[only_image]': 1 },
        }}
    />
)

/**
 * メディアライブラリからファイル選択（全ファイル対象）
 */
export const MediaFile = ({ readonly = false, disabled = false, ...props }) => (
    <MediaSelector {...props} readonly={readonly} disabled={disabled} modalTitle="ファイルを選択" />
)

/**
 * 複数画像選択（配列で media_id を返す）
 */
export const MediaImages = ({
    defaultValue = [],
    onChange = () => {},
    readonly = false,
    disabled = false,
    ...props
}) => {
    const [items, setItems] = useState([])
    const [initializing, setInitializing] = useState(true)

    const [showModal, setShowModal] = useState(false)
    const [isUploading, setIsUploading] = useState(false)
    const resourceIndexRef = useRef(null)
    const sensors = useSensors(useSensor(PointerSensor, { activationConstraint: { distance: 5 } }))
    const apiClient = createAuthenticatedAxios()
    const { replacePath } = useCompanyFacility()

    const handleUploadComplete = useCallback(() => {
        resourceIndexRef.current?.refresh()
        toast.success('ファイルをアップロードしました')
    }, [])

    const handleAdd = useCallback(
        (item) => {
            setItems((prev) => {
                const next = [...prev, item]
                onChange(
                    next.map((i) => i.id),
                    next
                )
                return next
            })
            setShowModal(false)
        },
        [onChange]
    )

    const handleRemove = useCallback(
        (idx) => {
            setItems((prev) => {
                const next = prev.filter((_, i) => i !== idx)
                onChange(
                    next.map((i) => i.id),
                    next
                )
                return next
            })
        },
        [onChange]
    )

    const moveItem = useCallback(
        (from, to) => {
            if (from === to || from < 0 || to < 0) return
            setItems((prev) => {
                const next = [...prev]
                const [moved] = next.splice(from, 1)
                next.splice(to, 0, moved)
                onChange(
                    next.map((i) => i.id),
                    next
                )
                return next
            })
        },
        [onChange]
    )

    // 初期値がID配列の場合にメディア詳細を取得してプレビュー可能にする
    useEffect(() => {
        let isMounted = true
        const bootstrap = async () => {
            try {
                const arr = Array.isArray(defaultValue) ? defaultValue : []
                if (arr.length === 0) {
                    setInitializing(false)
                    return
                }

                // 既にオブジェクトが入っている場合はそのまま採用
                if (typeof arr[0] === 'object') {
                    if (!isMounted) return
                    setItems(arr)
                    return
                }

                // ID配列の場合は詳細を取得
                const results = await Promise.all(
                    arr.map((id) => apiClient.get(`${replacePath(mediaConfig.end_point)}/${id}`))
                )
                const fetched = results.map((r) => r?.data?.payload?.data).filter((x) => !!x)
                if (!isMounted) return
                setItems(fetched)
            } catch (e) {
                // 失敗時は空のまま
                console.error(e)
            } finally {
                if (isMounted) setInitializing(false)
            }
        }
        bootstrap()
        return () => {
            isMounted = false
        }
    }, [defaultValue])

    const ids = items.map((item, idx) => `${item.id}_${idx}`)

    const handleDragEnd = (event) => {
        const { active, over } = event
        if (!over || active.id === over.id) return
        const oldIndex = ids.findIndex((id) => id === active.id)
        const newIndex = ids.findIndex((id) => id === over.id)
        const updated = arrayMove(items, oldIndex, newIndex)
        setItems(updated)
        onChange(
            updated.map((i) => i.id),
            updated
        )
    }

    const SortableRow = ({ id, children, readonly = false }) => {
        const { attributes, listeners, setNodeRef, transform, transition, isDragging } =
            useSortable({ id, disabled: readonly })
        const style = {
            transform: CSS.Transform.toString(transform),
            transition,
            background: isDragging ? '#f3f4f6' : undefined,
            opacity: isDragging ? 0.9 : 1,
        }
        return (
            <div
                ref={setNodeRef}
                style={style}
                className={`flex items-center gap-3 ${isDragging ? 'opacity-90' : ''}`}
            >
                {!readonly && (
                    <span
                        className="cursor-grab text-gray-400 hover:text-gray-600"
                        style={{ touchAction: 'none' }}
                        {...attributes}
                        {...listeners}
                    >
                        <HiMenu size={18} />
                    </span>
                )}
                {children}
            </div>
        )
    }

    const columns = [
        { key: 'btns', label: '', sortable: false, _props: { style: { width: '8%' } } },
        { key: 'file', label: '', sortable: false, _props: { style: { width: '80px' } } },
        { key: 'file_name', label: 'ファイル名' },
        { key: 'mime_type', label: '形式', _props: { style: { width: '10%' } } },
        { key: 'file_size', label: 'サイズ', _props: { style: { width: '10%' } } },
        { key: 'created_at', label: '作成日', _props: { style: { width: '15%' } } },
    ]

    const addScopedColumns = {
        file: (item, row, idx) => (
            <td key={idx} className="text-center p-2">
                {isImage(item.mime_type) ? (
                    <div className="flex justify-center">
                        <img
                            src={item.file_url}
                            alt={item.file_name}
                            className="w-12 h-12 object-cover rounded border"
                        />
                    </div>
                ) : (
                    <div className="flex justify-center">{getFileIcon(item.mime_type)}</div>
                )}
            </td>
        ),
        file_name: (item) => (
            <td>
                <span className="text-gray-700 dark:text-gray-200">{item.file_name}</span>
            </td>
        ),
        file_size: (item, row, idx) => (
            <td className="text-end pe-2" key={idx}>
                {getFileBytes(item.file_size)}
            </td>
        ),
        created_at: (item) => (
            <td className="text-center">{dayjs(item.created_at).format('YYYY/MM/DD HH:mm:ss')}</td>
        ),
        btns: (item, row, idx) => (
            <td key={idx} className="text-end">
                {!readonly && !disabled && (
                    <div className="flex justify-center w-full">
                        <Button size="xs" outline onClick={() => handleAdd(item)}>
                            追加
                        </Button>
                    </div>
                )}
            </td>
        ),
    }

    return (
        <div className="space-y-3">
            <DndContext
                sensors={sensors}
                collisionDetection={closestCenter}
                onDragEnd={handleDragEnd}
                modifiers={[restrictToVerticalAxis]}
            >
                <SortableContext items={ids} strategy={verticalListSortingStrategy}>
                    <div className="flex flex-col gap-3">
                        {initializing && <div className="text-sm text-gray-500">読み込み中...</div>}
                        {!initializing && items.length === 0 && (
                            <div className="text-sm text-gray-500">未選択</div>
                        )}
                        {items.map((item, idx) => (
                            <SortableRow
                                key={ids[idx]}
                                id={ids[idx]}
                                readonly={readonly || disabled}
                            >
                                {isImage(item?.mime_type) ? (
                                    <img
                                        src={item.file_url}
                                        alt={item.file_name}
                                        className="w-12 h-12 object-cover rounded border"
                                    />
                                ) : (
                                    <div className="w-12 h-12 flex items-center justify-center bg-gray-100 rounded border">
                                        {getFileIcon(item?.mime_type)}
                                    </div>
                                )}
                                <div className="flex-1 min-w-0">
                                    <div className="text-sm text-gray-800 truncate">
                                        <a
                                            href={item.file_url}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            {item.file_name}
                                        </a>
                                    </div>
                                    <div className="text-xs text-gray-500">
                                        {item.mime_type} ・ {getFileBytes(item.file_size)}
                                    </div>
                                </div>
                                {!readonly && !disabled && (
                                    <div className="flex items-center gap-1">
                                        <Button
                                            size="xs"
                                            color="red"
                                            outline
                                            onClick={() => handleRemove(idx)}
                                        >
                                            削除
                                        </Button>
                                    </div>
                                )}
                            </SortableRow>
                        ))}
                    </div>
                </SortableContext>
            </DndContext>

            {!readonly && !disabled && (
                <div>
                    <Button outline onClick={() => setShowModal(true)} size="xs">
                        メディアライブラリから追加する
                    </Button>
                </div>
            )}

            <Modal
                show={showModal}
                onClose={() => setShowModal(false)}
                size="5xl"
                dismissible={!isUploading}
            >
                <ModalHeader>メディアを選択</ModalHeader>
                <ModalBody>
                    <div className="mb-4">
                        <FileUploader
                            endpoint={':company_alias/:facility_alias/media_library/store'}
                            onUploadStateChange={setIsUploading}
                            onUploadComplete={handleUploadComplete}
                            acceptedFileTypes={{
                                'image/*': ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.svg'],
                            }}
                        />
                    </div>
                    <ResourceIndex
                        ref={resourceIndexRef}
                        options={{
                            breads: [{ name: mediaConfig.name }],
                            config: {
                                ...mediaConfig,
                                path: replacePath(mediaConfig.path),
                                end_point: replacePath(mediaConfig.end_point),
                            },
                            columns,
                            isNew: false,
                            isEdit: false,
                            isDelete: false,
                            skeletonRow: 3,
                            addScopedColumns,
                            baseParams: { 'criteria[only_image]': 1 },
                        }}
                    />
                </ModalBody>
            </Modal>
        </div>
    )
}
