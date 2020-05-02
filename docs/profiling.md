# Profiling

Encyclopedia allows for application-level code profiling.
This is useful during development, but also during production - to find bottlenecks (usually database or cache).

Profiling can be set to either full, which incurs a performance penalty.
This penalty varies quite a lot, but it allows for more precise measurements.

The other option is **very** slim, but safe to use in even constrained environments (say, a Raspberry Pi or 1vcpu/512mb vm).
Sample interval for this option is by default 0.1 seconds.

## Setup

To enable profiling, you must install the [xhprof](https://pecl.php.net/package/xhprof) extension for php.

Using pecl, it's as easy as

```bash
pecl install xhprof
```

Then enable it in your `php.ini` file (tip: run `php --ini` to get the location)

```ini
[xhprof]
xhprof.enable=1
```

## Configuration

To enable profiling and optionally customize related configuration,
you use the env variables as listed below. 

| Name | Description | Default | Options |
|------|-------------|---------|---------|
| PROFILING_ENABLED | Enable/Disable profiling | `false` | `true`/`false` |

## Visualising reports

On the dashboard under the metrics section, click on _reports_.
There you'll find the generated reports.

These can be filtered by category cli/request and command/route signature.

**TODO:** Add screenshots.
