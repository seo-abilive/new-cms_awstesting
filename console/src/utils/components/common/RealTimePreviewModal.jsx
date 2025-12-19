import React, { useState, useEffect, useRef, useCallback } from 'react'
import { Modal, ModalHeader, ModalBody } from '@/utils/components/ui/modal'
import { FormComp } from '@/utils/components/ui/form'
import { useAxios } from '@/utils/hooks/useAxios'
import { Spinner } from '@/utils/components/ui/spinner'
import { Alert } from '@/utils/components/ui/alert'
import { HiDesktopComputer, HiDeviceTablet, HiDeviceMobile } from 'react-icons/hi'
import { useCompanyFacility } from '@/utils/context/CompanyFacilityContext'
import { Button } from '../ui/button'

/**
 * リアルタイムプレビューモーダルコンポーネント
 * 左ペインに入力フォーム、右ペインにプレビュー画面を表示
 *
 * @param {Object} props
 * @param {boolean} props.show モーダルの表示状態
 * @param {Function} props.onClose モーダルを閉じるコールバック
 * @param {Object} props.inputs 現在の入力値
 * @param {Array} props.formItem フォームフィールド定義
 * @param {string} props.previewUrl プレビューAPIのURL
 * @param {Function} props.onInputChange 入力値変更時のコールバック
 */
export const RealTimePreviewModal = ({
    show,
    onClose,
    inputs: initialInputs,
    formItem,
    previewUrl,
    onInputChange,
    endpoint,
}) => {
    const [previewInputs, setPreviewInputs] = useState(initialInputs || {})
    const [previewHtml, setPreviewHtml] = useState('')
    const [previewError, setPreviewError] = useState(null)
    const [deviceType, setDeviceType] = useState('desktop') // 'desktop', 'tablet', 'mobile'
    const { loading, sendRequest } = useAxios()
    const { company_alias } = useCompanyFacility()
    const debounceTimerRef = useRef(null)
    const iframeRef = useRef(null)

    // プレビューHTML内の相対パスを絶対パスに変換
    const convertRelativePathsToAbsolute = useCallback((html, baseUrl) => {
        if (!html || !baseUrl) return html

        try {
            // ベースURLからドメインとパスを取得
            const url = new URL(baseUrl)
            // baseOriginを明示的に構築（ポート番号を確実に含める）
            let baseOrigin = url.origin
            // new-cms-demoの場合、ポート番号が含まれていない場合は:80を追加
            if (url.hostname === 'new-cms-demo' && !url.port) {
                baseOrigin = `${url.protocol}//${url.hostname}:80`
            } else if (url.hostname === 'new-cms-demo' && url.port !== '80') {
                // ポート番号が80以外の場合は80に変更
                baseOrigin = `${url.protocol}//${url.hostname}:80`
            } else if (url.hostname === 'new-cms-demo-next' && !url.port) {
                baseOrigin = `${url.protocol}//${url.hostname}:5174`
            } else if (url.hostname === 'new-cms-demo-next' && url.port !== '5174') {
                baseOrigin = `${url.protocol}//${url.hostname}:5174`
            }
            const basePath = url.pathname.substring(0, url.pathname.lastIndexOf('/') + 1)

            // HTML内の相対パスを絶対パスに変換
            let convertedHtml = html

            // Docker環境対応: iframe内からアクセス可能なURLに変換
            // iframeのsrcdoc内では、Dockerコンテナ名（new-cms-demo）ではアクセスできないため、
            // ブラウザからアクセス可能なlocalhost:8082に変換する
            // 注意: 既にlocalhost:8082になっているものは変換しない（重複変換を防ぐ）

            // まず、new-cms-demo:80をlocalhost:8082に変換
            convertedHtml = convertedHtml.replace(
                /http:\/\/new-cms-demo:80(\/)?/g,
                'http://localhost:8082$1'
            )
            convertedHtml = convertedHtml.replace(
                /https:\/\/new-cms-demo:80(\/)?/g,
                'http://localhost:8082$1'
            )
            // new-cms-demo:8082をlocalhost:8082に変換
            convertedHtml = convertedHtml.replace(
                /http:\/\/new-cms-demo:8082(\/)?/g,
                'http://localhost:8082$1'
            )
            convertedHtml = convertedHtml.replace(
                /https:\/\/new-cms-demo:8082(\/)?/g,
                'http://localhost:8082$1'
            )
            // new-cms-demo（ポート番号なし）をlocalhost:8082に変換
            convertedHtml = convertedHtml.replace(
                /http:\/\/new-cms-demo(\/|")/g,
                'http://localhost:8082$1'
            )
            convertedHtml = convertedHtml.replace(
                /https:\/\/new-cms-demo(\/|")/g,
                'http://localhost:8082$1'
            )

            convertedHtml = convertedHtml.replace(
                /http:\/\/new-cms-demo-next(\/)?/g,
                'http://localhost:5174$1'
            )
            convertedHtml = convertedHtml.replace(
                /https:\/\/new-cms-demo-next(\/)?/g,
                'http://localhost:5174$1'
            )

            // 127.0.0.1:8082をlocalhost:8082に統一（既にlocalhost:8082のものは変換しない）
            convertedHtml = convertedHtml.replace(
                /http:\/\/127\.0\.0\.1:8082(\/)?/g,
                'http://localhost:8082$1'
            )

            // まず、既にlocalhost:808282になっているものを修正
            convertedHtml = convertedHtml.replace(/localhost:808282/g, 'localhost:8082')

            // CSSリンク（href属性）- 相対パスのみ変換
            convertedHtml = convertedHtml.replace(
                /(<link[^>]+href=["'])(?!https?:\/\/|data:|#)([^"']+)(["'][^>]*>)/gi,
                (match, prefix, path, suffix) => {
                    const absolutePath = path.startsWith('/')
                        ? `${baseOrigin}${path}`
                        : `${baseOrigin}${basePath}${path}`
                    return `${prefix}${absolutePath}${suffix}`
                }
            )

            // 画像、スクリプトなどのsrc属性 - 相対パスのみ変換
            convertedHtml = convertedHtml.replace(
                /(<(?:img|script|source|video|audio|iframe|embed)[^>]+src=["'])(?!https?:\/\/|data:|#)([^"']+)(["'][^>]*>)/gi,
                (match, prefix, path, suffix) => {
                    const absolutePath = path.startsWith('/')
                        ? `${baseOrigin}${path}`
                        : `${baseOrigin}${basePath}${path}`
                    return `${prefix}${absolutePath}${suffix}`
                }
            )

            // CSS内のurl()関数（background-imageなど）- 相対パスのみ変換
            convertedHtml = convertedHtml.replace(
                /url\(["']?(?!https?:\/\/|data:|#)([^"')]+)["']?\)/gi,
                (match, path) => {
                    const absolutePath = path.startsWith('/')
                        ? `${baseOrigin}${path}`
                        : `${baseOrigin}${basePath}${path}`
                    return `url('${absolutePath}')`
                }
            )

            return convertedHtml
        } catch (err) {
            console.warn('パス変換エラー:', err)
            return html
        }
    }, [])

    // モーダルが開いたときに初期値を設定
    useEffect(() => {
        if (show) {
            setPreviewInputs(initialInputs || {})
            setPreviewHtml('')
            setPreviewError(null)
            // 初回プレビューを取得
            fetchPreview(initialInputs || {})
        }
    }, [show])

    // initialInputsが変更されたときにpreviewInputsを更新（モーダルが開いている間は反映しない）
    useEffect(() => {
        if (show && initialInputs) {
            // モーダルが開いている間は、外部からの変更を反映しない
            // （モーダル内での変更を優先するため）
        } else if (!show) {
            // モーダルが閉じている間は、initialInputsの変更を反映
            setPreviewInputs(initialInputs || {})
        }
    }, [initialInputs, show])

    // モーダルを閉じる際に、最終的な状態を親に通知
    const handleClose = useCallback(() => {
        // モーダル内で変更された最終的な状態を親に通知
        // handleInputChangeで既にリアルタイムで更新されているが、
        // 念のため最終的な状態を確実に反映する
        if (onInputChange && previewInputs) {
            // 最後のフィールドの変更として、すべての入力値を一度に親に通知
            const lastFormId = Object.keys(previewInputs).pop()
            if (lastFormId) {
                onInputChange(lastFormId, previewInputs[lastFormId], previewInputs)
            }
        }
        onClose()
    }, [onClose, onInputChange, previewInputs])

    // 入力値が変更されたときにプレビューを更新（デバウンス）
    useEffect(() => {
        if (!show) return

        // 既存のタイマーをクリア
        if (debounceTimerRef.current) {
            clearTimeout(debounceTimerRef.current)
        }

        // 500ms後にプレビューを取得
        debounceTimerRef.current = setTimeout(() => {
            fetchPreview(previewInputs)
        }, 500)

        // クリーンアップ
        return () => {
            if (debounceTimerRef.current) {
                clearTimeout(debounceTimerRef.current)
            }
        }
    }, [previewInputs, show])

    // プレビューAPIを呼び出す
    const fetchPreview = useCallback(
        async (data) => {
            if (!previewUrl) return

            try {
                setPreviewError(null)

                // 完全なURL（http://またはhttps://で始まる）の場合はプロキシ経由で取得
                const isExternalUrl =
                    previewUrl.startsWith('http://') || previewUrl.startsWith('https://')
                let requestUrl = previewUrl
                let requestData = data

                if (isExternalUrl && company_alias) {
                    // プロキシエンドポイントを使用
                    // baseURLに既にapi/admin/が含まれているため、company_aliasから始める
                    requestUrl = `${endpoint}/preview`
                    requestData = {
                        preview_url: previewUrl,
                        data: data,
                    }
                }

                const result = await sendRequest({
                    method: 'POST',
                    url: requestUrl,
                    data: requestData,
                })

                if (result.success && result.data) {
                    // レスポンスがHTML文字列の場合
                    let html = ''
                    if (typeof result.data === 'string') {
                        html = result.data
                    } else if (result.data.html) {
                        html = result.data.html
                    } else if (result.data.payload?.html) {
                        html = result.data.payload.html
                    } else {
                        // JSONレスポンスの場合は文字列化して表示
                        html = `<pre>${JSON.stringify(result.data, null, 2)}</pre>`
                    }

                    // 相対パスを絶対パスに変換（iframe内でCSS/画像が読み込めるように）
                    // プレビューURLからベースURLを取得（Docker環境対応）
                    // iframe内からはlocalhost:8082でアクセス可能
                    let baseUrl = 'http://localhost:8082'
                    if (previewUrl.includes('localhost:5174')) {
                        baseUrl = 'http://localhost:5174'
                    }

                    const convertedHtml = convertRelativePathsToAbsolute(html, baseUrl)

                    // iframeが既に存在する場合は、スクロール位置を保持しながら内容を更新
                    if (iframeRef.current?.contentWindow?.document && convertedHtml) {
                        try {
                            const iframeWindow = iframeRef.current.contentWindow
                            const iframeDoc = iframeWindow.document

                            // 現在のスクロール位置を保存
                            const scrollX = iframeWindow.scrollX || 0
                            const scrollY = iframeWindow.scrollY || 0

                            // HTMLをパースしてheadとbodyを分離
                            const parser = new DOMParser()
                            const newDoc = parser.parseFromString(convertedHtml, 'text/html')

                            // スクロール位置を復元する関数
                            const restoreScroll = () => {
                                try {
                                    if (iframeWindow) {
                                        iframeWindow.scrollTo(scrollX, scrollY)
                                    }
                                } catch (e) {
                                    // エラーは無視
                                }
                            }

                            // headの更新をスキップ（チカチカを防ぐため）
                            // 初回読み込み時のみheadが設定され、以降はbodyのみを更新

                            // bodyの内容を直接置き換え（チカチカを防ぐため、一度に更新）
                            const existingBody = iframeDoc.body
                            if (existingBody && newDoc.body) {
                                // スクロール位置を保持しながらbodyの内容を置き換え
                                // チカチカを防ぐため、更新前に非表示にして、更新後に表示
                                requestAnimationFrame(() => {
                                    try {
                                        // 更新前にbodyを非表示（レイアウトは保持）
                                        const originalVisibility = existingBody.style.visibility
                                        existingBody.style.visibility = 'hidden'

                                        // 次のフレームで内容を更新
                                        requestAnimationFrame(() => {
                                            try {
                                                // bodyの内容を一度に置き換え
                                                existingBody.innerHTML = newDoc.body.innerHTML

                                                // スクロール位置を即座に復元
                                                restoreScroll()

                                                // 更新後にbodyを表示
                                                requestAnimationFrame(() => {
                                                    existingBody.style.visibility =
                                                        originalVisibility || 'visible'
                                                    restoreScroll()
                                                })
                                            } catch (e) {
                                                // エラー時も表示を復元
                                                existingBody.style.visibility =
                                                    originalVisibility || 'visible'
                                                throw e
                                            }
                                        })
                                    } catch (e) {
                                        throw e
                                    }
                                })
                            } else {
                                // bodyが存在しない場合は通常の方法で更新
                                iframeDoc.open()
                                iframeDoc.write(convertedHtml)
                                iframeDoc.close()
                                restoreScroll()
                            }

                            // 複数のタイミングでスクロール位置を復元
                            requestAnimationFrame(restoreScroll)
                            requestAnimationFrame(() => {
                                requestAnimationFrame(restoreScroll)
                            })

                            // 画像やCSSの読み込み完了後に再度復元
                            iframeWindow.addEventListener('load', restoreScroll, { once: true })

                            // 複数回試行して確実に復元
                            setTimeout(restoreScroll, 0)
                            setTimeout(restoreScroll, 10)
                            setTimeout(restoreScroll, 50)
                            setTimeout(restoreScroll, 100)
                            setTimeout(restoreScroll, 200)

                            // 状態も更新（次回の更新のために）
                            setPreviewHtml(convertedHtml)
                        } catch (e) {
                            // クロスオリジンエラーなどの場合は、通常の方法で更新
                            console.warn('iframe直接更新に失敗、通常の方法で更新:', e)
                            setPreviewHtml(convertedHtml)
                        }
                    } else {
                        // 初回表示時は通常通り
                        setPreviewHtml(convertedHtml)
                    }
                } else {
                    // エラーメッセージを取得
                    const errorMessage =
                        result.data?.payload?.error ||
                        result.data?.error ||
                        'プレビューの取得に失敗しました'

                    // デバッグ情報をコンソールに出力
                    console.error('プレビュー取得エラー:', {
                        error: errorMessage,
                        url: result.data?.payload?.url || previewUrl,
                        status: result.data?.payload?.status,
                        fullResponse: result.data,
                    })

                    setPreviewError(errorMessage)
                }
            } catch (err) {
                console.error('プレビュー取得エラー:', err)
                setPreviewError('プレビューの取得中にエラーが発生しました')
            }
        },
        [previewUrl, sendRequest, company_alias, convertRelativePathsToAbsolute]
    )

    // 入力値の変更ハンドラー
    const handleInputChange = (formId, value) => {
        // カスタムブロックの場合は深いコピーを作成
        const newInputs = Array.isArray(value)
            ? { ...previewInputs, [formId]: JSON.parse(JSON.stringify(value)) }
            : { ...previewInputs, [formId]: value }
        setPreviewInputs(newInputs)
        // 親コンポーネントにも通知（オプション）
        if (onInputChange) {
            onInputChange(formId, value, newInputs)
        }
    }

    // メインエリアのフォームアイテム
    const mainFormItem = formItem?.filter(
        (item) => typeof item.position === 'undefined' || item.position !== 'aside'
    )

    // サイドエリアのフォームアイテム
    const asideFormItem = formItem?.filter(
        (item) => typeof item.position !== 'undefined' && item.position === 'aside'
    )

    // デバイスタイプに応じたプレビューコンテナのスタイル
    const getPreviewContainerStyle = () => {
        switch (deviceType) {
            case 'tablet':
                return {
                    width: '768px',
                    margin: '0 auto',
                }
            case 'mobile':
                return {
                    width: '375px',
                    margin: '0 auto',
                }
            case 'desktop':
            default:
                return {
                    width: '100%',
                    margin: '0',
                }
        }
    }

    return (
        <Modal show={show} onClose={handleClose} size="100vh">
            <ModalHeader className="flex items-center justify-between">
                <span>リアルタイムプレビュー</span>
            </ModalHeader>
            <ModalBody className="p-0">
                <div className="flex h-[calc(100vh-200px)]">
                    {/* 左ペイン：入力フォーム */}
                    <div className="w-1/3 border-r border-gray-200 dark:border-gray-700 overflow-y-auto p-4">
                        <div className="space-y-4">
                            {mainFormItem?.map((item, index) => (
                                <FormComp
                                    key={item.id}
                                    item={item}
                                    defaultValue={previewInputs?.[item.id]}
                                    validationErrors={null}
                                    onChange={(value) => {
                                        item?.onChangeItem && item.onChangeItem(value)
                                        handleInputChange(item.id, value)
                                    }}
                                    index={index}
                                    inputs={previewInputs}
                                    readonly={false}
                                />
                            ))}
                            {asideFormItem.length > 0 && (
                                <div className="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                    <h3 className="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">
                                        サイド情報
                                    </h3>
                                    {asideFormItem?.map((item, index) => (
                                        <FormComp
                                            key={item.id}
                                            item={item}
                                            defaultValue={previewInputs?.[item.id]}
                                            validationErrors={null}
                                            onChange={(value) => {
                                                handleInputChange(item.id, value)
                                            }}
                                            index={index}
                                            inputs={previewInputs}
                                            readonly={false}
                                        />
                                    ))}
                                </div>
                            )}
                        </div>
                    </div>

                    {/* 右ペイン：プレビュー画面 */}
                    <div className="w-2/3 overflow-y-auto bg-gray-50 dark:bg-gray-900 flex flex-col">
                        {/* デバイス切り替えタブ */}
                        <div className="border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2">
                            <div className="flex gap-2">
                                <Button
                                    onClick={() => setDeviceType('desktop')}
                                    color={deviceType === 'desktop' ? 'blue' : 'light'}
                                >
                                    <HiDesktopComputer className="w-5 h-5" />
                                    PC
                                </Button>
                                <Button
                                    onClick={() => setDeviceType('tablet')}
                                    color={deviceType === 'tablet' ? 'blue' : 'light'}
                                >
                                    <HiDeviceTablet className="w-5 h-5" />
                                    タブレット
                                </Button>
                                <Button
                                    onClick={() => setDeviceType('mobile')}
                                    color={deviceType === 'mobile' ? 'blue' : 'light'}
                                >
                                    <HiDeviceMobile className="w-5 h-5" />
                                    スマホ
                                </Button>
                            </div>
                        </div>

                        {/* プレビューコンテンツ */}
                        <div className="flex-1 overflow-y-auto p-4">
                            {previewError && (
                                <Alert color="failure" className="mt-4">
                                    {previewError}
                                </Alert>
                            )}
                            {!previewError && previewHtml && (
                                <div
                                    className="preview-content bg-white dark:bg-gray-800 shadow-lg"
                                    style={getPreviewContainerStyle()}
                                >
                                    <iframe
                                        ref={iframeRef}
                                        srcDoc={previewHtml}
                                        style={{
                                            width: '100%',
                                            height: '100%',
                                            minHeight: '600px',
                                            border: 'none',
                                            display: 'block',
                                        }}
                                        sandbox="allow-same-origin allow-scripts"
                                        title="プレビュー"
                                    />
                                </div>
                            )}
                            {!previewError && !previewHtml && (
                                <div className="flex items-center justify-center h-full min-h-[400px] text-gray-500">
                                    プレビューを読み込み中...
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </ModalBody>
        </Modal>
    )
}
