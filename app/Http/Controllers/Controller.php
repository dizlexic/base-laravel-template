<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Pusher\Pusher;
use Pusher\PusherException;
use Illuminate\Support\Str;

/**
 * Abstract base controller class for "OC Connect" Laravel application.
 * This controller encapsulates common methods for redirection, rendering views/modals,
 * handling messages, and other functionalities such as creating form request instances
 * and interfacing with Pusher.
 */
abstract class Controller
{
    // a protected property to store the message data
    // see: withError, withMessage, and withSuccess methods.
    protected ?array $message = null;

    /**
     * Redirect to the previous page.
     *
     * @param array $props - The props to pass to the previous page.
     *
     * @return RedirectResponse - The redirect response.
     */
    public function back(array $props = []): RedirectResponse
    {
        return redirect()->back()->with('props', $props);
    }

    /**
     * Render an Inertia view with the specified properties.
     *
     * @param string $view - The name of the Inertia view to render.
     * @param array $props - The properties to pass to the view.
     *
     * @return Response - The rendered Inertia response.
     */
    public function inertia(string $view, array $props = []): Response
    {
        return Inertia::render(
            str_contains($view, '.')
                ? $this->strToPath($view)
                : $view,
            $props
        );
    }


    /**
     * Get a new Pusher instance.
     *
     * @return Pusher - The Pusher instance.
     * @throws PusherException
     *
     */
    public function pusher(): Pusher
    {
        $connection = config('broadcasting.connections.reverb');

        return new Pusher(
            $connection['key'],
            $connection['secret'],
            $connection['app_id'],
            $connection['options'] ?? []
        );
    }

    /**
     * Redirect to a named route.
     *
     * Uses session middleware to pass props to the route.
     * see: App/Http/Middleware/HandleInertiaRequests.php line 34 for how this is passed to the frontend.
     *
     * @param string $route - The route to redirect to.
     * @param array $props - The props to pass to the route.
     * @param int $status - The status code to use for the redirect.
     *
     * @return RedirectResponse - The redirect response.
     */
    public function redirect(string $route, array $props = [], int $status = 302): RedirectResponse
    {
        if ($this->message) {
            $props['flash'] = $this->message;
        }

        // With sets the session data for the next request.
        return redirect($route, $status)->with('props', $props);
    }

    /**
     * Create a new form request instance.
     *
     * @param string $className - The class name of the request. IE LoginRequest
     *
     * @return mixed - The request instance.
     * @throws BindingResolutionException
     *
     * @throws AuthorizationException
     */
    public function requestFactory(string $className)
    {
        $req = app()->make($className);

        if (!$req->authorize()) {
            throw new AuthorizationException('unauthorized', 403);
        }

        return $req;
    }

    /**
     * Redirect to a named route with an error message.
     *
     * @param string $message - The error message to display.
     * @param string $id - The id of the message.
     * @param string $icon - The icon to display with the message.
     *
     * @return $this - The controller instance.
     */
    public function withError(
        string $message,
        string $id = 'error-message',
        string $icon = 'mdi-alert',
    ): self
    {
        return $this->withMessage($message, $id, 'error', $icon);
    }

    /**
     * Redirect to a named route with an info message.
     *
     * @param string $message - The info message to display.
     * @param string $id - The id of the message.
     * @param string $icon - The icon to display with the message.
     *
     * @return $this - The controller instance.
     */
    public function withMessage(
        string $message,
        string $id = 'message',
        string $type = 'info',
        string $icon = 'mdi-information-circle',
    ): self
    {
        $uuid = Str::uuid();
        $id = "{$uuid}-{$id}";

        $this->message = compact('id', 'type', 'message', 'icon');

        return $this;
    }

    /**
     * Redirect to a named route with a success message.
     *
     * @param string $message - The success message to display.
     * @param string $id - The id of the message.
     * @param string $icon - The icon to display with the message.
     *
     * @return $this - The controller instance.
     */
    public function withSuccess(
        string $message,
        string $id = 'success-message',
        string $icon = 'mdi-check',
    ): self
    {
        return $this->withMessage($message, $id, 'success', $icon);
    }

    /**
     * Convert a string to a specific path format.
     *
     * @param string $str - The input string to be converted.
     * @param bool $forceConvert - Whether to force the conversion regardless of the string format.
     *
     * @return string - The converted path string or the original string.
     */
    protected function strToPath(string $str, bool $forceConvert = false): string
    {
        return str_contains($str, '.') || $forceConvert ? implode(
            '/',
            array_map(
                static fn($part) => implode(
                    '',
                    array_map(
                        'ucfirst',
                        explode(
                            '-',
                            $part
                        )
                    )
                ),
                explode('.', $str)
            )
        ) : $str;
    }
}
