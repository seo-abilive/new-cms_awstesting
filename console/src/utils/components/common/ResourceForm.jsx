import React, {
    useEffect,
    useState,
    useMemo,
    forwardRef,
    useImperativeHandle,
    useRef,
    useCallback,
} from 'react'
import { useAxios } from '@/utils/hooks/useAxios'
import { useNavigation } from '@/utils/hooks/useNavigation'
import { Form, FormBuilder, FormComp, FormGroup, Label } from '@/utils/components/ui/form'
import { Card, CardBody, CardFooter, CardHeader } from '@/utils/components/ui/card'
import { BreadNavigation } from '@/utils/components/ui/breadcrumb'
import { Alert } from '@/utils/components/ui/alert'
import { Button } from '@/utils/components/ui/button'
import { Spinner } from '@/utils/components/ui/spinner'
import { HiOutlineEye, HiOutlineSave } from 'react-icons/hi'
import { Col, Row } from '@/utils/components/ui/grid'
import { toast } from 'sonner'
import axios from 'axios'
import { RealTimePreviewModal } from './RealTimePreviewModal'

/**
 * 汎用リソースフォームコンポーネント（作成・編集）。
 *
 * @component
 * @param {Object} props
 * @param {Object} props.options 設定オプション
 * @param {Array} [props.options.breads=[]] パンくずリスト
 * @param {Object} props.options.config リソースの設定（end_point, path など）
 * @param {string} [props.options.id=null] 編集対象ID（nullなら新規作成）
 * @param {Array} props.options.formItem フォームフィールド定義（id, title, type などを含む）
 */
export const ResourceForm = forwardRef(({ options }, ref) => {
    const {
        breads = [],
        config,
        id = null,
        formItem = [],
        readonly = false,
        isRealTimePreview = false,
        previewUrl = null,
    } = options
    const { navigateTo } = useNavigation()
    const { error, loading, validationErrors, sendRequest } = useAxios()
    const [isLoaded, setIsLoaded] = useState(!id)
    const [showValidationAlert, setShowValidationAlert] = useState(false)
    const [showPreviewModal, setShowPreviewModal] = useState(false)
    const [formKey, setFormKey] = useState(0) // フォームコンポーネントの再マウント用

    // 外部から呼び出せるメソッドを定義
    useImperativeHandle(
        ref,
        () => ({
            getInputs: () => inputs,
            setInputs: (inputs) => setInputs(inputs),
            setInputVal: (formId, value) => setInputVal(formId, value),
            getValidationErrors: () => validationErrors,
            setValidationErrors: (validationErrors) => setValidationErrors(validationErrors),
            getError: () => error,
            setError: (error) => setError(error),
            getLoading: () => loading,
            setLoading: (loading) => setLoading(loading),
        }),
        []
    )

    // バリデーションエラーの件数をカウント
    const errorCount = validationErrors
        ? Object.keys(validationErrors).reduce(
              (count, key) =>
                  count + (Array.isArray(validationErrors[key]) ? validationErrors[key].length : 1),
              0
          )
        : 0

    // バリデーションエラーが表示されたときにスクロール
    useEffect(() => {
        if (errorCount > 0 || (error && !validationErrors)) {
            setShowValidationAlert(true)
            // ページ上部にスクロール（すぐに実行）
            setTimeout(() => {
                window.scrollTo({ top: 0, behavior: 'smooth' })
            }, 100)
        } else {
            setShowValidationAlert(false)
        }
    }, [errorCount, error, validationErrors])

    const [inputs, setInputs] = useState(() => {
        const result = {}
        formItem.forEach((item) => {
            result[item.id] = item?.default ?? ''
        })
        return result
    })

    // メインエリアのフォーム
    const mainFormItem = useMemo(
        () =>
            formItem?.filter(
                (item) => typeof item.position === 'undefined' || item.position !== 'aside'
            ),
        [formItem]
    )

    // サイドエリアのフォーム
    const aSideFormItem = useMemo(
        () =>
            formItem?.filter(
                (item) => typeof item.position !== 'undefined' && item.position === 'aside'
            ),
        [formItem]
    )

    // configとformItemをrefで保持（無限ループを防ぐため）
    const configRef = useRef(config)
    const formItemRef = useRef(formItem)
    const navigateToRef = useRef(navigateTo)

    useEffect(() => {
        configRef.current = config
        formItemRef.current = formItem
        navigateToRef.current = navigateTo
    }, [config, formItem, navigateTo])

    useEffect(() => {
        if (!id) return

        // 編集データ取得
        const fetch = async () => {
            setIsLoaded(false)
            try {
                const response = await sendRequest({
                    method: 'GET',
                    url: `${configRef.current.end_point}/${id}`,
                })
                if (response && response.success && response.data) {
                    const data = {}
                    formItemRef.current.forEach((item) => {
                        if (typeof item.onFetch !== 'undefined') {
                            data[item.id] = item.onFetch(
                                response.data.payload.data[item.id],
                                response.data.payload.data
                            )
                        } else {
                            data[item.id] = response.data.payload.data[item.id]
                        }
                    })
                    setInputs(data)
                } else if (response && !response.success) {
                    // エラーが発生した場合（404など）
                    const errorMessage =
                        axios.isAxiosError(response.error) &&
                        response.error.response?.status === 404
                            ? 'データが見つかりませんでした。'
                            : 'データの取得に失敗しました。'

                    toast.error(errorMessage)
                    navigateToRef.current(configRef.current.path)
                }
            } catch (err) {
                // 予期しないエラー
                const errorMessage =
                    axios.isAxiosError(err) && err.response?.status === 404
                        ? 'データが見つかりませんでした。'
                        : 'データの取得に失敗しました。'

                toast.error(errorMessage)
                navigateToRef.current(configRef.current.path)
            } finally {
                setIsLoaded(true)
            }
        }
        fetch()
    }, [id, sendRequest])

    const setInputVal = (formId, value) => {
        setInputs((prev) => {
            if (prev[formId] === value) return prev
            return { ...prev, [formId]: value } // prevを使用するように修正
        })
    }

    const handleSubmit = async (event) => {
        event.preventDefault()
        const result = await sendRequest({
            method: !id ? 'post' : 'put',
            url: !id ? `${config.end_point}/store` : `${config.end_point}/${id}`,
            data: inputs,
        })

        if (result.success) {
            // 成功したらindexへ
            navigateTo(config.path, {
                message: !id ? `作成しました。` : `更新しました。`,
            })
        }
    }

    return (
        <>
            <Card>
                <CardHeader>
                    <div className="flex items-center justify-between w-full">
                        <div className="flex items-center gap-4 flex-1">
                            <BreadNavigation breads={breads} />
                        </div>
                        {isRealTimePreview && previewUrl && (
                            <div>
                                <Button size="xs" outline onClick={() => setShowPreviewModal(true)}>
                                    <HiOutlineEye className="me-1" />
                                    プレビュー
                                </Button>
                            </div>
                        )}
                    </div>
                </CardHeader>
                <CardBody>
                    {!isLoaded ? (
                        <>
                            <Spinner />
                        </>
                    ) : (
                        <Form onSubmit={handleSubmit}>
                            <div className="flex flex-col gap-4">
                                {showValidationAlert && errorCount > 0 && (
                                    <Alert color="failure">
                                        <span className="font-semibold">
                                            {errorCount}件の入力エラーがあります
                                        </span>
                                    </Alert>
                                )}
                                {showValidationAlert &&
                                    error &&
                                    (!validationErrors ||
                                        (validationErrors &&
                                            Object.keys(validationErrors).length === 0)) && (
                                        <Alert color="failure">
                                            エラーが発生しました: {error.message}
                                        </Alert>
                                    )}
                                <Row cols={12}>
                                    <Col col={aSideFormItem.length > 0 ? 9 : 12}>
                                        {mainFormItem?.map((item, index) => (
                                            <FormComp
                                                key={`${item.id}-${formKey}`}
                                                item={item}
                                                defaultValue={inputs?.[item.id]}
                                                validationErrors={validationErrors}
                                                onChange={(value) => {
                                                    item?.onChangeItem && item.onChangeItem(value)
                                                    setInputVal(item.id, value)
                                                }}
                                                index={index}
                                                inputs={inputs}
                                                readonly={readonly}
                                            />
                                        ))}
                                    </Col>
                                    {aSideFormItem.length > 0 && (
                                        <Col col={3} className="ms-4 ps-4 border-s">
                                            {aSideFormItem?.map((item, index) => (
                                                <FormComp
                                                    key={`${item.id}-${formKey}`}
                                                    item={item}
                                                    defaultValue={inputs?.[item.id]}
                                                    validationErrors={validationErrors}
                                                    onChange={(value) => {
                                                        setInputVal(item.id, value)
                                                    }}
                                                    index={index}
                                                    inputs={inputs}
                                                    readonly={readonly}
                                                />
                                            ))}
                                        </Col>
                                    )}
                                </Row>
                            </div>
                        </Form>
                    )}
                </CardBody>
                {isLoaded && !readonly && (
                    <CardFooter>
                        <div className="flex justify-end">
                            <Button size="xs" outline onClick={handleSubmit} disabled={loading}>
                                {loading ? (
                                    <Spinner size="sm" />
                                ) : (
                                    <HiOutlineSave className="me-1" />
                                )}
                                保存
                            </Button>
                        </div>
                    </CardFooter>
                )}
            </Card>
            {isRealTimePreview && previewUrl && (
                <RealTimePreviewModal
                    show={showPreviewModal}
                    onClose={() => {
                        setShowPreviewModal(false)
                        // モーダルを閉じたときにフォームコンポーネントを再マウントして、
                        // プレビューで入力した内容を確実に反映
                        setFormKey((prev) => prev + 1)
                    }}
                    inputs={inputs}
                    formItem={formItem}
                    previewUrl={previewUrl}
                    endpoint={config.end_point}
                    onInputChange={(formId, value, newInputs) => {
                        // モーダル内での入力変更を親フォームにも反映
                        setInputs(newInputs)
                    }}
                />
            )}
        </>
    )
})
