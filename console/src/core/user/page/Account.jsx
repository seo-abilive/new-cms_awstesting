import { useEffect, useRef, useState } from 'react'
import { useAuth } from '@/utils/context/AuthContext'
import { useAxios } from '@/utils/hooks/useAxios'
import { TextInput } from '@/utils/components/ui/form/TextInput'
import { Label } from '@/utils/components/ui/form'
import { Button } from '@/utils/components/ui/button'
import { Spinner } from '@/utils/components/ui/spinner'
import { Alert } from '@/utils/components/ui/alert'
import { toast } from 'sonner'
import { Switch } from '@/utils/components/ui/form/Switch'
import { Card, CardBody, CardFooter, CardHeader } from '@/utils/components/ui/card'
import { BreadNavigation } from '@/utils/components/ui/breadcrumb'

export const Account = () => {
    const { user, refresh } = useAuth()
    const { sendRequest, loading, error, validationErrors } = useAxios()
    const ref = useRef({ name: '', email: '', password: '', password_confirmation: '' })
    const [isChangePassword, setIsChangePassword] = useState(false)

    useEffect(() => {
        if (user) {
            ref.current.name = user.name || ''
            ref.current.email = user.email || ''
        }
    }, [user])

    const onSubmit = async () => {
        await sendRequest({
            method: 'GET',
            url: `${
                import.meta.env.VITE_API_ORIGIN || 'http://localhost:8000'
            }/sanctum/csrf-cookie`,
        })
        const payload = { name: ref.current.name, email: ref.current.email }
        if (isChangePassword) {
            payload.password = ref.current.password
            payload.password_confirmation = ref.current.password_confirmation
        }
        const res = await sendRequest({ method: 'PUT', url: `user/me`, data: payload })
        if (res?.success) {
            toast.success('アカウント情報を更新しました。')
            await refresh()
        }
    }

    return (
        <div>
            <Card>
                <CardHeader>
                    <BreadNavigation breads={[{ name: 'アカウント設定' }]} />
                </CardHeader>
                <CardBody>
                    {error && (
                        <Alert color="failure" className="mb-2">
                            {error.response?.data?.message || 'エラーが発生しました'}
                        </Alert>
                    )}
                    <div className="space-y-6 max-w-xl">
                        <div>
                            <Label>名前</Label>
                            <TextInput
                                defaultValue={user?.name}
                                onChange={(v) => (ref.current.name = v)}
                            />
                            {validationErrors?.name && (
                                <p className="text-error-500">{validationErrors.name[0]}</p>
                            )}
                        </div>
                        <div>
                            <Label>メールアドレス</Label>
                            <TextInput
                                type="email"
                                autocomplete="email"
                                defaultValue={user?.email}
                                onChange={(v) => (ref.current.email = v)}
                            />
                            {validationErrors?.email && (
                                <p className="text-error-500">{validationErrors.email[0]}</p>
                            )}
                        </div>
                        <div className="pt-2">
                            <label className="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer select-none">
                                <Switch
                                    defaultValue={isChangePassword}
                                    onChange={(v) => {
                                        setIsChangePassword(v)
                                        if (!v) {
                                            ref.current.password = ''
                                            ref.current.password_confirmation = ''
                                        }
                                    }}
                                    label="パスワードを変更する"
                                />
                            </label>
                        </div>
                        {isChangePassword && (
                            <>
                                <div>
                                    <Label>新しいパスワード</Label>
                                    <TextInput
                                        type="password"
                                        autocomplete="new-password"
                                        onChange={(v) => (ref.current.password = v)}
                                    />
                                    {validationErrors?.password && (
                                        <p className="text-error-500">
                                            {validationErrors.password[0]}
                                        </p>
                                    )}
                                </div>
                                <div>
                                    <Label>新しいパスワード（確認）</Label>
                                    <TextInput
                                        type="password"
                                        autocomplete="new-password"
                                        onChange={(v) => (ref.current.password_confirmation = v)}
                                    />
                                </div>
                            </>
                        )}
                    </div>
                </CardBody>
                <CardFooter>
                    <div className="flex justify-end">
                        <Button onClick={onSubmit} disabled={loading} size="xs" outline>
                            {loading ? <Spinner className="size-4" /> : '保存'}
                        </Button>
                    </div>
                </CardFooter>
            </Card>
        </div>
    )
}
