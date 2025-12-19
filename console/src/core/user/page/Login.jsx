import { Button } from '@/utils/components/ui/button'
import { Label } from '@/utils/components/ui/form'
import { TextInput } from '@/utils/components/ui/form/TextInput'
import { useAxios } from '@/utils/hooks/useAxios'
import { EyeCloseIcon, EyeIcon } from '@/utils/icons'
import { useRef, useState, useEffect } from 'react'
import { Link } from 'react-router'
import { config, USER_TYPE } from '../utils/config'
import appConfig from '@/config/configLoader'
import { useNavigation } from '@/utils/hooks/useNavigation'
import { Spinner } from '@/utils/components/ui/spinner'
import { Alert } from '@/utils/components/ui/alert'
import { useAuth } from '@/utils/context/AuthContext'

export const Login = () => {
    const [showPassword, setShowPassword] = useState(false)
    const [requiresTwoFactor, setRequiresTwoFactor] = useState(false)
    const [verificationCode, setVerificationCode] = useState('')
    const [resendCooldown, setResendCooldown] = useState(0)
    const { loading, sendRequest, validationErrors, error } = useAxios()
    const inputRef = useRef({ email: '', password: '' })
    const cooldownIntervalRef = useRef(null)
    const { navigateTo } = useNavigation()
    const { user, refresh } = useAuth()

    // クールダウンタイマーのクリーンアップ
    useEffect(() => {
        return () => {
            if (cooldownIntervalRef.current) {
                clearInterval(cooldownIntervalRef.current)
                cooldownIntervalRef.current = null
            }
        }
    }, [])

    // requiresTwoFactorがfalseになったときにタイマーをクリーンアップ
    useEffect(() => {
        if (!requiresTwoFactor) {
            if (cooldownIntervalRef.current) {
                clearInterval(cooldownIntervalRef.current)
                cooldownIntervalRef.current = null
            }
            setResendCooldown(0)
        }
    }, [requiresTwoFactor])

    // 入力内容の変更
    const onHandleChange = (key, value) => {
        inputRef.current[key] = value
    }

    // ログイン成功後のリダイレクト処理
    const handleLoginSuccess = async () => {
        // 認証状態を即時反映し、user_type に応じて既定遷移先を決定
        const me = await refresh()
        const meData = me?.data
        const userType = meData?.user_type || user?.user_type
        // 既定のリダイレクト先を user_type ごとに構築
        const firstCompanyAlias = meData?.companies?.[0]?.alias
        const firstFacilityAlias = meData?.facilities?.[0]?.alias
        const facilityCompanyAlias = meData?.facilities?.[0]?.company?.alias || firstCompanyAlias
        let redirect = '/master/'
        if (userType === USER_TYPE.MANAGE && firstCompanyAlias) {
            redirect = `/manage/${firstCompanyAlias}/master/`
        } else if (userType === USER_TYPE.FACILITY && facilityCompanyAlias && firstFacilityAlias) {
            redirect = `/manage/${facilityCompanyAlias}/${firstFacilityAlias}/`
        } else if (userType === USER_TYPE.MANAGE) {
            redirect = '/manage/'
        }
        try {
            const saved = localStorage.getItem('postLoginRedirect')
            if (saved && typeof saved === 'string') {
                // 保存側は相対パス（basename 除去済み）に修正済みだが、
                // 念のため basename 付きで保存された旧データにも対応
                const base = appConfig?.basename || '/'
                const baseNormalized = base.endsWith('/') ? base : `${base}/`
                const normalized = saved.startsWith(baseNormalized)
                    ? `/${saved.slice(baseNormalized.length)}`
                    : saved
                redirect = normalized || redirect
            }
            localStorage.removeItem('postLoginRedirect')
        } catch (_) {}
        // /login に留まらないようにフォールバック
        const base = appConfig?.basename || '/'
        const baseNormalized = base.endsWith('/') ? base : `${base}/`
        const isLoginPath =
            redirect === '/login' ||
            redirect.startsWith('/login') ||
            redirect === `${baseNormalized}login` ||
            redirect.startsWith(`${baseNormalized}login`)

        if (!redirect || isLoginPath) {
            // 上で決めたデフォルトを使用
            // それでも空なら最後のフォールバック
            redirect = redirect || (userType === USER_TYPE.MANAGE ? '/manage/' : '/master/')
        }
        // navigate には basename なしのパスを渡す（Router に basename が設定されているため）
        const to = redirect.startsWith(baseNormalized)
            ? `/${redirect.slice(baseNormalized.length)}`
            : redirect
        navigateTo(to)
    }

    // フォーム送信
    const onHandleSubmit = async (e) => {
        // CSRF Cookie を取得（Sanctum SPA）
        await sendRequest({
            method: 'GET',
            url: `${
                import.meta.env.VITE_API_ORIGIN || 'http://localhost:8000'
            }/sanctum/csrf-cookie`,
        })

        // ログイン処理（Cookie ベース）
        const result = await sendRequest({
            method: 'POST',
            url: `${config.end_point}/login`,
            data: inputRef.current,
        })

        if (result?.success) {
            // APIレスポンスの構造: { success: true, timestamp: ..., payload: { ... } }
            // sendRequestは { success: true, data: response.data } を返す
            const responseData = result?.data || result
            const payload = responseData?.payload

            // デバッグ用（本番環境では削除）
            console.log('Login response:', { result, responseData, payload })

            // 2段階認証が必要な場合
            if (payload?.requires_two_factor) {
                setRequiresTwoFactor(true)
                return
            }
            // 通常のログイン成功
            await handleLoginSuccess()
        }
    }

    // 認証コード検証
    const onHandleVerifyCode = async () => {
        // CSRF Cookie を取得（Sanctum SPA）
        await sendRequest({
            method: 'GET',
            url: `${
                import.meta.env.VITE_API_ORIGIN || 'http://localhost:8000'
            }/sanctum/csrf-cookie`,
        })

        // 認証コード検証
        const result = await sendRequest({
            method: 'POST',
            url: `${config.end_point}/login/verify-code`,
            data: {
                email: inputRef.current.email,
                password: inputRef.current.password,
                code: verificationCode,
            },
        })

        if (result?.success) {
            // 検証成功後、認証コードをクリア
            setVerificationCode('')
            await handleLoginSuccess()
        }
    }

    // 認証コード再送信
    const onHandleResendCode = async () => {
        if (resendCooldown > 0) {
            return
        }

        // CSRF Cookie を取得（Sanctum SPA）
        await sendRequest({
            method: 'GET',
            url: `${
                import.meta.env.VITE_API_ORIGIN || 'http://localhost:8000'
            }/sanctum/csrf-cookie`,
        })

        // 認証コード再送信
        const result = await sendRequest({
            method: 'POST',
            url: `${config.end_point}/login/resend-code`,
            data: {
                email: inputRef.current.email,
            },
        })

        if (result?.success) {
            // 既存のタイマーをクリア
            if (cooldownIntervalRef.current) {
                clearInterval(cooldownIntervalRef.current)
            }

            // 再送信成功後、60秒のクールダウンを設定
            setResendCooldown(60)
            cooldownIntervalRef.current = setInterval(() => {
                setResendCooldown((prev) => {
                    if (prev <= 1) {
                        if (cooldownIntervalRef.current) {
                            clearInterval(cooldownIntervalRef.current)
                            cooldownIntervalRef.current = null
                        }
                        return 0
                    }
                    return prev - 1
                })
            }, 1000)
        }
    }
    // 自動遷移は onHandleSubmit 内でのみ行い、二重リダイレクトを防止

    // 2段階認証画面
    if (requiresTwoFactor) {
        return (
            <>
                <div className="mb-5 sm:mb-8">
                    <h1 className="mb-2 font-semibold text-gray-800 text-title-sm dark:text-white/90 sm:text-title-md">
                        認証コード入力
                    </h1>
                    <p className="text-sm text-gray-500 dark:text-gray-400">
                        メールに送信された6桁の認証コードを入力してください
                    </p>
                </div>
                <div>
                    {error && (
                        <Alert color="failure" className="mb-2">
                            {error?.response?.data?.message ||
                                error?.message ||
                                'エラーが発生しました'}
                        </Alert>
                    )}
                    <form autoComplete="off">
                        <div className="space-y-6">
                            <div>
                                <Label>
                                    認証コード<span className="text-error-500">*</span>{' '}
                                </Label>
                                <TextInput
                                    type="text"
                                    autocomplete="off"
                                    maxLength={6}
                                    placeholder="000000"
                                    value={verificationCode}
                                    onChange={(value) => {
                                        // 数字のみ入力可能
                                        const numericValue = value
                                            .replace(/[^0-9]/g, '')
                                            .slice(0, 6)
                                        setVerificationCode(numericValue)
                                    }}
                                    onKeyUp={(e) => {
                                        if (e.key === 'Enter' && verificationCode.length === 6) {
                                            onHandleVerifyCode()
                                        }
                                    }}
                                    style={{
                                        textAlign: 'center',
                                        fontSize: '1.5rem',
                                        letterSpacing: '0.5rem',
                                        fontFamily: 'monospace',
                                    }}
                                />
                                {validationErrors?.code && (
                                    <p className="text-error-500">{validationErrors.code[0]}</p>
                                )}
                                <p className="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                    認証コードは30分間有効です
                                </p>
                            </div>
                            <div>
                                <Button
                                    className="w-full"
                                    size="sm"
                                    onClick={onHandleVerifyCode}
                                    disabled={loading || verificationCode.length !== 6}
                                >
                                    {loading ? <Spinner className="size-4" /> : '認証コードを確認'}
                                </Button>
                            </div>
                            <div className="text-center">
                                <button
                                    type="button"
                                    onClick={onHandleResendCode}
                                    disabled={loading || resendCooldown > 0}
                                    className="text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400 disabled:text-gray-400 disabled:cursor-not-allowed"
                                >
                                    {resendCooldown > 0
                                        ? `認証コードを再送信（${resendCooldown}秒）`
                                        : '認証コードを再送信'}
                                </button>
                            </div>
                            <div className="text-center">
                                <button
                                    type="button"
                                    onClick={() => {
                                        // タイマーをクリーンアップ
                                        if (cooldownIntervalRef.current) {
                                            clearInterval(cooldownIntervalRef.current)
                                            cooldownIntervalRef.current = null
                                        }
                                        // 状態をリセット
                                        setRequiresTwoFactor(false)
                                        setVerificationCode('')
                                        setResendCooldown(0)
                                    }}
                                    className="text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400"
                                >
                                    ログイン画面に戻る
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </>
        )
    }

    // 通常のログイン画面
    return (
        <>
            <div className="mb-5 sm:mb-8">
                <h1 className="mb-2 font-semibold text-gray-800 text-title-sm dark:text-white/90 sm:text-title-md">
                    Login
                </h1>
                <p className="text-sm text-gray-500 dark:text-gray-400">
                    メールアドレス・パスワードを入力して、ログインして下さい
                </p>
            </div>
            <div>
                {error && (
                    <Alert color="failure" className="mb-2">
                        {error?.response?.data?.message || error?.message || 'エラーが発生しました'}
                    </Alert>
                )}
                <form autoComplete="off">
                    <div className="space-y-6">
                        <div>
                            <Label>
                                メールアドレス<span className="text-error-500">*</span>{' '}
                            </Label>
                            <TextInput
                                type="email"
                                autocomplete="email"
                                onChange={(value) => onHandleChange('email', value)}
                                onKeyUp={(e) => {
                                    if (e.key === 'Enter') {
                                        onHandleSubmit()
                                    }
                                }}
                            />
                            {validationErrors?.email && (
                                <p className="text-error-500">{validationErrors.email[0]}</p>
                            )}
                        </div>
                        <div className="relative">
                            <Label>
                                パスワード<span className="text-error-500">*</span>{' '}
                            </Label>
                            <TextInput
                                type={showPassword ? 'text' : 'password'}
                                autocomplete="current-password"
                                onChange={(value) => onHandleChange('password', value)}
                                onKeyUp={(e) => {
                                    if (e.key === 'Enter') {
                                        onHandleSubmit()
                                    }
                                }}
                            />
                            <span
                                onClick={() => setShowPassword(!showPassword)}
                                className="absolute z-30 -translate-y-1/2 cursor-pointer right-4 top-13.5"
                            >
                                {showPassword ? (
                                    <EyeIcon className="fill-gray-500 dark:fill-gray-400 size-5" />
                                ) : (
                                    <EyeCloseIcon className="fill-gray-500 dark:fill-gray-400 size-5" />
                                )}
                            </span>
                            {validationErrors?.password && (
                                <p className="text-error-500">{validationErrors.password[0]}</p>
                            )}
                        </div>
                        <div className="flex justify-end">
                            <Link
                                to="/reset-password"
                                className="text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400"
                            >
                                パスワードを忘れた方はこちら
                            </Link>
                        </div>
                        <div>
                            <Button
                                className="w-full"
                                size="sm"
                                onClick={onHandleSubmit}
                                disabled={loading}
                            >
                                {loading ? <Spinner className="size-4" /> : 'ログイン'}
                            </Button>
                        </div>
                    </div>
                </form>
            </div>
        </>
    )
}
